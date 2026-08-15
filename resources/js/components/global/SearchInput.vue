<template>
    <div class="search-input" :class="{ 'is-open': desplegableAbierto }">
        <div class="search-input-field">
            <i class="las la-search search-input-icono" aria-hidden="true"></i>
            <input
                ref="campo"
                v-model="searchKeyword"
                class="search-input-input"
                :class="{ 'show-input': showInput }"
                :placeholder="placeholder"
                type="search"
                autocomplete="off"
                role="combobox"
                aria-autocomplete="list"
                :aria-expanded="desplegableAbierto ? 'true' : 'false'"
                @input="alEscribir"
                @focus="alEnfocar"
                @keydown.down.prevent="mover(1)"
                @keydown.up.prevent="mover(-1)"
                @keydown.enter="alPulsarEnter"
                @keydown.esc="cerrar"
            />
            <button
                v-if="searchKeyword"
                type="button"
                class="search-input-clear"
                aria-label="Limpiar búsqueda"
                @click="limpiar"
            >
                <i class="las la-times"></i>
            </button>
        </div>

        <button class="search-input-button" type="button" @click.stop.prevent="search()">
            <SearchIcon />
            <span class="search-input-label ml-2">{{ buttonLabel }}</span>
        </button>

        <!-- Sugerencias en vivo -->
        <div v-if="desplegableAbierto" class="search-suggest" role="listbox">
            <div v-if="cargando" class="search-suggest-estado">
                <v-progress-circular indeterminate size="20" width="2" color="#f58634" />
                <span>Buscando…</span>
            </div>

            <template v-else-if="resultados.length">
                <router-link
                    v-for="(p, i) in resultados"
                    :key="p.id"
                    :to="{ name: 'ProductDetails', params: { slug: p.slug } }"
                    class="search-suggest-item"
                    :class="{ 'is-active': i === indiceActivo }"
                    role="option"
                    :aria-selected="i === indiceActivo ? 'true' : 'false'"
                    @click.native="cerrar"
                    @mouseenter.native="indiceActivo = i"
                >
                    <img
                        class="search-suggest-img"
                        :src="p.thumbnail_image || $helpers.imagePlaceholder()"
                        :alt="p.name"
                        @error="imageFallback($event)"
                    />
                    <span class="search-suggest-datos">
                        <span class="search-suggest-nombre">{{ p.name }}</span>
                        <span v-if="precioDe(p)" class="search-suggest-precio">{{ precioDe(p) }}</span>
                    </span>
                </router-link>

                <button type="button" class="search-suggest-todos" @click="search()">
                    Ver todos los resultados de “{{ searchKeyword }}”
                </button>
            </template>

            <div v-else class="search-suggest-estado">
                <i class="las la-search"></i>
                <span>Sin resultados para “{{ searchKeyword }}”</span>
            </div>
        </div>
    </div>
</template>

<script>
import SearchIcon from "../icons/Search.vue";

export default {
    name: "SearchInput",
    props: {
        placeholder: {
            type: String,
            default: "Escribe para buscar"
        },
        buttonLabel: {
            type: String,
            default: "Buscar"
        },
        showInput: {
            type: Boolean,
            default: false
        },
        // Numero de sugerencias a mostrar
        maxSugerencias: {
            type: Number,
            default: 6
        }
    },
    components: {
        SearchIcon
    },
    data() {
        return {
            searchKeyword: "",
            resultados: [],
            cargando: false,
            enfocado: false,
            indiceActivo: -1,
            temporizador: null,
            // Identifica cada peticion para descartar respuestas que llegan
            // tarde y pisarian a una busqueda mas reciente.
            peticionId: 0
        };
    },
    computed: {
        desplegableAbierto() {
            return this.enfocado && this.searchKeyword.trim().length >= 2;
        }
    },
    mounted() {
        document.addEventListener("click", this.alClicarFuera);
    },
    beforeDestroy() {
        document.removeEventListener("click", this.alClicarFuera);
        if (this.temporizador) clearTimeout(this.temporizador);
    },
    methods: {
        precioDe(p) {
            const valor = p.base_discounted_price || p.base_price || 0;
            if (!valor) return null;
            return valor.toLocaleString("es-CO", {
                style: "currency",
                currency: "COP",
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        },
        alEnfocar() {
            this.enfocado = true;
            if (this.searchKeyword.trim().length >= 2 && !this.resultados.length) {
                this.buscarSugerencias();
            }
        },
        alClicarFuera(e) {
            if (!this.$el.contains(e.target)) this.cerrar();
        },
        cerrar() {
            this.enfocado = false;
            this.indiceActivo = -1;
        },
        limpiar() {
            this.searchKeyword = "";
            this.resultados = [];
            this.indiceActivo = -1;
            this.$refs.campo && this.$refs.campo.focus();
        },
        alEscribir() {
            this.enfocado = true;
            this.indiceActivo = -1;

            if (this.temporizador) clearTimeout(this.temporizador);

            const termino = this.searchKeyword.trim();
            if (termino.length < 2) {
                this.resultados = [];
                this.cargando = false;
                return;
            }

            // Se espera a que la persona deje de teclear para no lanzar una
            // peticion por cada pulsacion.
            this.cargando = true;
            this.temporizador = setTimeout(this.buscarSugerencias, 300);
        },
        async buscarSugerencias() {
            const termino = this.searchKeyword.trim();
            if (termino.length < 2) return;

            const id = ++this.peticionId;
            this.cargando = true;

            try {
                const res = await this.call_api(
                    "get",
                    `product/search?form=search&limit=${this.maxSugerencias}&keyword=${encodeURIComponent(termino)}`
                );

                // Descarta respuestas obsoletas: sin esto, una peticion lenta
                // de "pa" podia sobreescribir los resultados de "papel".
                if (id !== this.peticionId) return;

                const datos = (res && res.data && res.data.products && res.data.products.data) || [];
                this.resultados = datos.slice(0, this.maxSugerencias);
            } catch (e) {
                if (id === this.peticionId) this.resultados = [];
            } finally {
                if (id === this.peticionId) this.cargando = false;
            }
        },
        mover(paso) {
            if (!this.resultados.length) return;
            const total = this.resultados.length;
            this.indiceActivo = (this.indiceActivo + paso + total) % total;
        },
        alPulsarEnter() {
            // Si hay una sugerencia resaltada con el teclado, se abre esa;
            // si no, se va al listado completo.
            if (this.indiceActivo >= 0 && this.resultados[this.indiceActivo]) {
                const p = this.resultados[this.indiceActivo];
                this.cerrar();
                this.$router
                    .push({ name: "ProductDetails", params: { slug: p.slug } })
                    .catch(() => {});
                return;
            }
            this.search();
        },
        search() {
            this.cerrar();
            this.$router
                .push({
                    name: "Search",
                    params: this.searchKeyword.length > 0 ? { keyword: this.searchKeyword } : {},
                    query: {
                        page: 1
                    }
                })
                .catch(() => {});
        }
    }
};
</script>

<style lang="scss" scoped>
/* Pastilla ligera. Antes: caja rectangular con sombra marcada y un bloque
   naranja con la palabra "BUSCAR", que en el header se comia el ancho. */
.search-input {
    position: relative;
    display: flex;
    align-items: center;

    width: 100%;
    max-width: 836px;
    /* 44px: minimo tactil recomendado. Antes 42 con el campo interno a 34,
       que en movil obligaba a apuntar. */
    height: 46px;

    background-color: #f4f6f8;
    border: 1.5px solid transparent;
    border-radius: 999px;
    padding: 3px 3px 3px 14px;

    transition: background-color 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;

    /* Se ilumina al escribir: la parte "dinamica" que faltaba. */
    &:focus-within {
        background-color: #ffffff;
        border-color: #f58634;
        box-shadow: 0 0 0 4px rgba(245, 134, 52, 0.15);
    }

    &.is-open {
        background-color: #ffffff;
        border-color: #f58634;
    }

    &-field {
        position: relative;
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        background: transparent;
    }

    &-icono {
        flex-shrink: 0;
        font-size: 19px;
        color: #9aa0a6;
        transition: color 0.18s ease;
    }

    &:focus-within &-icono {
        color: #f58634;
    }

    &-input {
        flex: 1;
        min-width: 0;

        height: 38px;

        font-family: "Roboto";
        font-size: 14px;
        letter-spacing: 0.2px;
        color: #25292e;

        background-color: transparent;
        border: 0;

        outline: none;
        padding: 0;

        appearance: none;
        -webkit-appearance: none;

        &::placeholder {
            color: #9aa0a6;
        }

        /* Oculta la X nativa del type=search: ya hay un boton propio */
        &::-webkit-search-cancel-button {
            display: none;
        }
    }

    &-clear {
        flex-shrink: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 0;
        background: transparent;
        color: #9aa0a6;
        cursor: pointer;

        &:hover {
            color: #25292e;
        }
    }

    &-label {
        /* La etiqueta "Buscar" se oculta en pantallas estrechas, pero el
           campo NO: antes el input entero desaparecia por debajo de 700px y
           en movil el buscador del header quedaba inservible. */
        display: none;

        @include respond-up("lg") {
            display: block;
        }
    }

    &-button {
        /* Boton circular compacto en vez del bloque naranja a toda altura */
        height: 38px;
        min-width: 38px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border: none;
        border-radius: 999px;

        color: #ffffff;
        background-color: #f58634 !important;

        padding: 0 9px;

        transition: background-color 0.2s ease-in-out, transform 0.15s ease;

        cursor: pointer;

        &:active {
            transform: scale(0.94);
        }

        /* La etiqueta solo aparece cuando de verdad sobra sitio */
        @include respond-up("lg") {
            padding: 0 16px;
        }

        &:hover {
            background-color: #e97318 !important;
        }

        &:focus {
            outline: none;
        }

        ::v-deep svg {
            display: flex;
            align-items: center;
            justify-content: center;

            path {
                fill: #ffffff;
            }
        }

        span {
            color: #ffffff;

            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            line-height: 1;

            margin-top: 1px;

            @include respond-up("md") {
                font-size: 14px;
            }
        }
    }
}

/* ---- Desplegable de sugerencias ---- */
.search-suggest {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    right: 0;
    z-index: 60;

    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 8px 28px rgba(0, 0, 0, 0.18);
    overflow: hidden;

    max-height: 60vh;
    overflow-y: auto;
}

.search-suggest-estado {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 16px;
    color: #6b7176;
    font-size: 14px;
}

.search-suggest-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    text-decoration: none;
    color: #25292e;
    border-bottom: 1px solid #f1f3f5;
    transition: background-color 0.12s ease;

    &:last-of-type {
        border-bottom: 0;
    }

    &.is-active,
    &:hover {
        background-color: #fff4ea;
    }
}

.search-suggest-img {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    object-fit: contain;
    background: #f7f8f9;
    border-radius: 6px;
}

.search-suggest-datos {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.search-suggest-nombre {
    font-size: 13px;
    line-height: 1.3;
    /* Los nombres de producto aqui son larguisimos: se limitan a 2 lineas */
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.search-suggest-precio {
    font-size: 13px;
    font-weight: 700;
    color: #f58634;
    margin-top: 2px;
}

.search-suggest-todos {
    width: 100%;
    border: 0;
    background: #fafbfc;
    color: #25292e;
    font-size: 13px;
    font-weight: 600;
    padding: 11px 12px;
    cursor: pointer;
    border-top: 1px solid #eceff1;

    &:hover {
        background: #f1f3f5;
        color: #f58634;
    }
}

.show-input {
    display: block !important;
}

.v-application {
    &.theme--dark {
        .search-input {
            background-color: #1f1f1f;

            &-field,
            &-input {
                background-color: #1f1f1f;
                color: #ffffff;
            }

            &-input::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }
        }
    }
}
</style>
