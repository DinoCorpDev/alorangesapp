<template>
    <div class="animate__zoomInDown">
        <div>
            <button v-if="showModal" class="btn-close" @click="toggleModal(false)">X</button>
            <div v-else>
                <a
                    class="float"
                    :href="whatsappUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Abrir chat de WhatsApp"
                ></a>
                <button class="float-message" type="button" @click="toggleModal(true)">Necesita ayuda?</button>
            </div>
        </div>

        <div v-if="showModal" class="response what-chat">
            <div class="wa-panel">
                <div class="wa-panel-header">
                    <div class="wa-panel-avatar"></div>
                    <div class="wa-panel-heading">
                        <strong>¡Háblanos por WhatsApp!</strong>
                        <span>Te atienden <b>humanos de verdad,</b> rápidos, amables y sin respuestas de robot.</span>
                    </div>
                </div>

                <!-- WhatsApp Links -->
                <a
                    :href="whatsappUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="wa-panel-contact"
                >
                    <div class="wa-panel-contact-avatar"></div>
                    <div class="wa-panel-contact-info">
                        <strong>Eduardo Ramirez</strong>
                        <span>+57 3174420109</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showModal: false,
            phoneNumber: "573174420109",
            message: "Hola *Aloranges* me gustaría recibir más información"
        };
    },
    computed: {
        whatsappUrl() {
            return `https://wa.me/${this.phoneNumber}?text=${encodeURIComponent(this.message)}`;
        }
    },
    methods: {
        toggleModal(state) {
            this.showModal = state;
        }
    }
};
</script>

<style>
/* whats */

.response.what-chat {
  position: fixed;
  width: 320px;
  bottom: 105px;
  right: 40px;
  z-index: 10001;
}

.wa-panel {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px, rgba(0, 0, 0, 0.22) 0px 10px 10px;
}

.wa-panel-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background-color: #2db742;
  color: #fff;
}

.wa-panel-avatar,
.wa-panel-contact-avatar {
  flex: 0 0 auto;
  background-color: #fff;
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  background-image: url('/public/images/WhatsappIcon.png');
  border-radius: 50%;
}

.wa-panel-avatar {
  width: 44px;
  height: 44px;
}

.wa-panel-heading {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.wa-panel-heading strong {
  font-size: 15px;
  font-weight: 700;
}

.wa-panel-heading span {
  font-size: 12px;
  font-weight: 400;
  line-height: 1.4;
  opacity: 0.95;
}

.wa-panel-contact {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  color: #111 !important;
  text-decoration: none !important;
  transition: background-color .15s ease;
}

.wa-panel-contact:hover {
  background-color: #f5f5f5;
}

.wa-panel-contact-avatar {
  width: 38px;
  height: 38px;
}

.wa-panel-contact-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.wa-panel-contact-info strong {
  font-size: 14px;
  font-weight: 600;
}

.wa-panel-contact-info span {
  font-size: 12px;
  color: #6b7280;
}

.float {
  position: fixed;
  display: block;
  background-size: contain;
    width: 68px;
    height: 68px;
    bottom: 15px;
    background-repeat: no-repeat;
    right: 40px;
    background-image: url('/public/images/WhatsappIcon.png');
    border-radius: 50px;
    text-align: center;
    box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px, rgba(0, 0, 0, 0.22) 0px 10px 10px;
    z-index: 10000;
  }

  .float-message {
    position: fixed;
    background-size: cover;
    width: 195px;
    height: 51px;
    bottom: 22px;
    background-color: #fff;
    background-repeat: no-repeat;
    right: 109px;
    border-radius: 5px;
    text-align: left;
    box-shadow: rgb(0 0 0 / 25%) 0px 14px 28px, rgb(0 0 0 / 22%) 0px 10px 10px;
    z-index: 10000;
    padding: 12px 19px;
    font-size: 18px;
    line-height: 1.3;
    border: 0;
    white-space: nowrap;
    cursor: pointer;
  }

.btn-close {
  position: fixed;
  width: 60px;
  height: 60px;
  bottom: 35px;
  right: 40px;
  background-color: #2db742 !important;
  border-radius: 50px;
  text-align: center;
  box-shadow: 1px 1px 2px #000;
  z-index: 10000;
  font-size: 30px;
  color: #fff;
}

/* RESPONSIVE */

@media only screen and (min-width: 320px) and (max-width: 767px) {
  .response {
    width: 75%;
    right: 30px;
  }

  .btn-close {
    right: 30px;
    bottom: 45px;
  }

  .float {
    right: 35px;
    bottom: 30px;
  }

  .float-message {
    bottom: 35px;
  }
}

@media only screen and (min-width: 767px) and (max-width: 820px) {
  .response {
    width: 42%;
    right: 20px;
    font-size: 14px;
  }

  .btn-close {
    right: 20px;
  }

  .float {
    right: 20px;
  }

  .wa-panel-contact-info span,
  .wa-panel-heading span {
    font-size: 12px;
  }
}
</style>
