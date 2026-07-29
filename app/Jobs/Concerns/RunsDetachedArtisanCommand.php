<?php

namespace App\Jobs\Concerns;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Starts an artisan command as a detached OS process, so long work survives the request
 * that asked for it without depending on a queue worker being up.
 *
 * Mirrors the approach already proven by SyncAlegraProductsJob::trigger().
 */
trait RunsDetachedArtisanCommand
{
    protected static function spawnDetachedArtisan(string $command): void
    {
        $phpBinary = self::resolvePhpBinary();
        $artisan = base_path('artisan');

        if (PHP_OS_FAMILY === 'Windows') {
            // On Windows a plain proc_open child is tied to the spawning process and dies
            // with it. "start /B" fully detaches it.
            $cmd = 'start "" /B ' . escapeshellarg($phpBinary) . ' ' . escapeshellarg($artisan) . ' ' . $command;
            $process = Process::fromShellCommandline($cmd);
            $process->setTimeout(null);
            $process->setIdleTimeout(null);
            $process->disableOutput();
            $process->start();

            return;
        }

        // Under PHP-FPM/LiteSpeed a plain proc_open child stays in the worker's process
        // group and can be killed when that worker is recycled, so it needs setsid + nohup.
        //
        // Symfony's Process wraps the command with its own exit-code/PID tracking, which is
        // incompatible with a command that backgrounds itself via a trailing "&" (the
        // wrapped command silently never runs). A raw proc_open() call avoids that.
        $cmd = 'setsid nohup ' . escapeshellarg($phpBinary) . ' ' . escapeshellarg($artisan) . ' ' . $command
            . ' > /dev/null 2>&1 &';

        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $proc = @proc_open($cmd, $descriptors, $pipes, base_path());

        if (is_resource($proc)) {
            foreach ($pipes as $pipe) {
                fclose($pipe);
            }
            proc_close($proc);
        }
    }

    /**
     * PhpExecutableFinder relies on PHP_BINARY / PATH, which are unreliable when PHP runs
     * in-process inside the web server (e.g. Apache + mod_php): PHP_BINARY then points at
     * httpd.exe, not a usable CLI binary, so find() returns empty.
     */
    protected static function resolvePhpBinary(): string
    {
        $isPhpCli = fn ($path) => $path && stripos(basename((string) $path), 'php') === 0;

        $found = (new PhpExecutableFinder)->find();
        if ($isPhpCli($found) && File::exists($found)) {
            return $found;
        }

        if ($isPhpCli(PHP_BINARY) && File::exists(PHP_BINARY)) {
            return PHP_BINARY;
        }

        // CloudLinux/LiteSpeed: PHP_BINARY points at the "lsphp" LSAPI binary
        // (e.g. /opt/alt/php83/usr/bin/lsphp), which isn't a general-purpose CLI runner.
        // Its sibling "php" binary in the same directory is.
        if (basename(PHP_BINARY) === 'lsphp' || str_contains(basename(PHP_BINARY), 'lsphp')) {
            $sibling = dirname(PHP_BINARY) . '/php';
            if (File::exists($sibling)) {
                return $sibling;
            }
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $candidate = 'C:/laragon/bin/php/php-' . PHP_VERSION . '/php.exe';
            if (File::exists($candidate)) {
                return $candidate;
            }

            $matches = glob('C:/laragon/bin/php/php-*/php.exe');
            if (! empty($matches)) {
                return $matches[0];
            }
        }

        return 'php';
    }
}
