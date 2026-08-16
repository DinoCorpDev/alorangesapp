<template>
    <!-- Antes esto era UN solo boton blanco que mezclaba cuenta y carrito
         separados por una linea fina, con un punto de estado sin etiqueta.
         Son dos acciones distintas, asi que ahora son dos botones, con el
         mismo lenguaje visual que el resto del header (icono + etiqueta). -->
    <div class="account-actions">
        <!-- Cuenta.
             Ni pastilla naranja ni texto plano: un boton circular que abre un
             menu. Ocupa 40px en vez de ~130px y, en lugar de una sola accion,
             ofrece entrar y crear cuenta (o los accesos de la cuenta si ya hay
             sesion), que antes habia que ir a buscar a otra parte. -->
        <v-menu offset-y left nudge-bottom="8" transition="slide-y-transition" min-width="210">
            <template #activator="{ on, attrs }">
                <button
                    type="button"
                    class="cuenta-boton"
                    :class="{ 'cuenta-boton--sesion': userIsLoggedIn }"
                    :aria-label="userIsLoggedIn ? 'Mi cuenta' : 'Iniciar sesión'"
                    v-bind="attrs"
                    v-on="on"
                >
                    <span class="cuenta-circulo">
                        <span v-if="userIsLoggedIn" class="cuenta-inicial">{{ inicialUsuario }}</span>
                        <i v-else class="las la-user cuenta-icono" aria-hidden="true"></i>
                        <span v-if="userIsLoggedIn" class="cuenta-punto" aria-hidden="true"></span>
                    </span>
                    <span class="cuenta-etiqueta">
                        {{ userIsLoggedIn ? userShortName || "Mi cuenta" : "Ingresar" }}
                    </span>
                </button>
            </template>

            <div class="cuenta-menu">
                <template v-if="userIsLoggedIn">
                    <div class="cuenta-menu-cabecera">
                        <span class="cuenta-menu-avatar">{{ inicialUsuario }}</span>
                        <span class="cuenta-menu-nombre">{{ userShortName || "Mi cuenta" }}</span>
                    </div>
                    <router-link :to="{ name: 'DashBoard' }" class="cuenta-menu-item">
                        <i class="las la-th-large" aria-hidden="true"></i> Mi panel
                    </router-link>
                    <router-link :to="{ name: 'PurchaseHistory' }" class="cuenta-menu-item">
                        <i class="las la-box" aria-hidden="true"></i> Mis pedidos
                    </router-link>
                    <router-link :to="{ name: 'Wishlist' }" class="cuenta-menu-item">
                        <i class="las la-heart" aria-hidden="true"></i> Mi lista
                    </router-link>
                    <router-link :to="{ name: 'Profile' }" class="cuenta-menu-item">
                        <i class="las la-user-cog" aria-hidden="true"></i> Mi perfil
                    </router-link>
                </template>

                <template v-else>
                    <div class="cuenta-menu-intro">
                        <strong>Hola 👋</strong>
                        <span>Entra para ver tus pedidos y precios</span>
                    </div>
                    <button type="button" class="cuenta-menu-principal" @click="showLoginDialog(true)">
                        Iniciar sesión
                    </button>
                    <button type="button" class="cuenta-menu-secundario" @click="irARegistro">
                        Crear cuenta
                    </button>
                </template>
            </div>
        </v-menu>

        <!-- Carrito -->
        <component
            :is="userIsLoggedIn ? 'router-link' : 'button'"
            :to="userIsLoggedIn ? { name: 'Cart' } : undefined"
            :type="userIsLoggedIn ? undefined : 'button'"
            class="account-action account-action--cart"
            title="Carrito"
            @click="!userIsLoggedIn && showLoginDialog(true)"
        >
            <span class="account-action-badge-wrap">
                <i class="las la-shopping-cart account-action-icono" aria-hidden="true"></i>
                <!-- El contador estaba suelto al lado del icono; ahora es una
                     insignia sobre el carrito, que es como se espera leerlo. -->
                <span v-if="getCartCount > 0" class="account-action-badge">
                    {{ getCartCount > 99 ? "99+" : getCartCount }}
                </span>
            </span>
            <span class="account-action-label">Carrito</span>
        </component>
    </div>
</template>

<script>
import { mapGetters, mapActions, mapMutations } from "vuex";

import CustomButton from "../global/CustomButton.vue";

import ShopCartIcon from "../icons/ShopCart.vue";
import Profile from "../icons/ProfileIcon.vue";

export default {
    components: {
        CustomButton,

        // Icons
        ShopCartIcon,
        Profile
    },
    data() {
        return {
            headerFixed: false,
            logoLarge: false,
            scrollThreshold: 10
        };
    },
    computed: {
        ...mapGetters("cart", ["getCartCount"]),
        ...mapGetters("auth", ["userShortName"]),
        ...mapGetters("auth", ["userIsLoggedIn"]),
        inicialUsuario() {
            const n = (this.userShortName || "").trim();
            return n ? n.charAt(0).toUpperCase() : "?";
        }
    },
    mounted() {
        // window.addEventListener("resize", this.handleScroll);
        //window.addEventListener("scroll", this.handleScroll, { passive: true });
    },
    methods: {
        ...mapMutations("auth", ["showLoginDialog"]),
        irARegistro() {
            this.$router.push({ name: "Registration" }).catch(() => {});
        }
        /*handleScroll() {
            const currentScroll = this.$refs.layoutNavbar.currentScroll;
            const windowWidth = window.innerWidth;

            this.headerFixed = currentScroll >= this.scrollThreshold;
            this.logoLarge = windowWidth < 960 ? false : this.headerFixed;
        }*/
    }
};
</script>

<style lang="scss" scoped>
// Dos acciones independientes, alineadas con el patron del header.
.account-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* El carrito comparte el mismo circulo que el boton de cuenta y que las
   acciones del navbar: un solo lenguaje visual en todo el header. Antes era
   icono + etiqueta "Carrito" en columna, un estilo distinto al resto. */
.account-action {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-width: 56px;
    padding: 2px 4px;
    border: 0;
    background: transparent;
    color: #3d4248;
    text-decoration: none;
    line-height: 1;
    cursor: pointer;
    transition: color 0.18s ease, transform 0.15s ease;

    /* El circulo envuelve solo al icono; la etiqueta va debajo */
    .account-action-badge-wrap {
        width: 40px;
        height: 40px;
        border: 1.5px solid #e3e7eb;
        border-radius: 50%;
        background: #ffffff;
        align-items: center;
        justify-content: center;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    &:hover,
    &:focus-visible {
        color: #f58634;

        .account-action-badge-wrap {
            border-color: #f58634;
            box-shadow: 0 4px 12px rgba(245, 134, 52, 0.22);
        }
    }

    &:active {
        transform: scale(0.95);
    }
}

.account-action-icono {
    font-size: 20px;
    line-height: 1;
}

/* ---------- Boton de cuenta ----------
   Identico a las demas acciones del header (mismo circulo blanco con borde y
   etiqueta debajo), con y SIN sesion. Antes, al iniciar sesion, pasaba a un
   circulo naranja con degradado y sin borde: rompia la fila. */
.cuenta-boton {
    position: relative;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-width: 56px;
    padding: 2px 4px;
    border: 0;
    background: transparent;
    color: #3d4248;
    cursor: pointer;
    transition: color 0.18s ease, transform 0.15s ease;

    .cuenta-circulo {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        border: 1.5px solid #e3e7eb;
        border-radius: 50%;
        background: #ffffff;
        transition: border-color 0.18s ease, box-shadow 0.18s ease;
    }

    &:hover,
    &:focus-visible {
        color: #f58634;

        .cuenta-circulo {
            border-color: #f58634;
            box-shadow: 0 4px 12px rgba(245, 134, 52, 0.22);
        }
    }

    &:active {
        transform: scale(0.95);
    }
}

.cuenta-icono {
    font-size: 22px;
    line-height: 1;
}

.cuenta-inicial {
    font-size: 15px;
    font-weight: 700;
    line-height: 1;
}

/* Punto verde: indica sesion iniciada de un vistazo */
/* Va anclado al circulo, no al boton entero (que ahora incluye la etiqueta) */
.cuenta-punto {
    position: absolute;
    right: -1px;
    bottom: -1px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #22c55e;
    box-shadow: 0 0 0 2px #ffffff;
}

/* Etiqueta bajo el icono, igual que el resto de acciones del header */
.cuenta-etiqueta {
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
    white-space: nowrap;
    /* El nombre del usuario puede ser largo: se recorta en vez de deformar
       la barra y empujar al resto de acciones. */
    max-width: 72px;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ---------- Menu desplegable ---------- */
.cuenta-menu {
    background: #ffffff;
    padding: 6px;
}

.cuenta-menu-intro {
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 10px 10px 12px;

    strong {
        font-size: 14px;
        color: #25292e;
    }

    span {
        font-size: 12px;
        color: #6b7176;
        line-height: 1.35;
    }
}

.cuenta-menu-principal {
    width: 100%;
    height: 38px;
    border: 0;
    border-radius: 8px;
    background: linear-gradient(135deg, #f9a05a 0%, #f58634 100%);
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: box-shadow 0.18s ease;

    &:hover {
        box-shadow: 0 4px 14px rgba(245, 134, 52, 0.4);
    }
}

.cuenta-menu-secundario {
    width: 100%;
    height: 34px;
    margin-top: 6px;
    border: 0;
    border-radius: 8px;
    background: transparent;
    color: #6b7176;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;

    &:hover {
        background: #f4f6f8;
        color: #25292e;
    }
}

.cuenta-menu-cabecera {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 10px 10px;
    border-bottom: 1px solid #eef1f4;
    margin-bottom: 4px;
}

.cuenta-menu-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    flex-shrink: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, #f9a05a 0%, #f58634 100%);
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
}

.cuenta-menu-nombre {
    font-size: 13px;
    font-weight: 700;
    color: #25292e;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.cuenta-menu-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 10px;
    border-radius: 8px;
    color: #3d4248;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: background-color 0.14s ease, color 0.14s ease;

    i {
        font-size: 17px;
        color: #9aa0a6;
    }

    &:hover {
        background: #fff4ea;
        color: #f58634;

        i {
            color: #f58634;
        }
    }
}

/* Etiqueta bajo el icono: al cliente le gusta ver el nombre de la accion */
.account-action-label {
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
    white-space: nowrap;
}

// Insignia con el numero de articulos, encima del icono del carrito.
.account-action-badge-wrap {
    position: relative;
    display: inline-flex;
}

.account-action-badge {
    position: absolute;
    top: -6px;
    right: -9px;
    min-width: 17px;
    height: 17px;
    padding: 0 4px;
    border-radius: 9px;
    background-color: #f58634;
    color: #ffffff;
    font-size: 10px;
    font-weight: 700;
    line-height: 17px;
    text-align: center;
    // Recorta el icono por debajo para que el numero siempre se lea.
    box-shadow: 0 0 0 2px #ffffff;
}
</style>
