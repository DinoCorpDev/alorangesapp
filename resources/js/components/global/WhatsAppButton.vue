<template>
    <div class="animate__zoomInDown">
        <div>
            <button v-if="showModal" class="btn-close" @click="toggleModal(false)">X</button>
            <div v-else>
                <button class="float" type="button" @click="toggleModal(true)"></button>
                <div class="float-message">Necesita ayuda?</div>
            </div>
        </div>

        <div v-if="showModal" class="response what-chat">
            <div class="row row-wha">
                <div class="col-12 header-wha">
                    <div class="row header-row">
                        <div class="col-3">
                            <div class="header-logo"></div>
                        </div>
                        <div class="col-9 text-left title-header">
                            ¡Háblanos por WhatsApp! <br /><br />
                            <span class="sub-header">
                              Te atienden <b>humanos de verdad,</b> rápidos, amables y sin respuestas de robot.
                            </span>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Links -->
                <a
                    href="https://wa.me/573174420109"
                    target="_blank"
                    rel="noreferrer"
                    class="col-12 box-wha"
                >
                    <div class="row items-box">
                        <div class="col-2">
                            <div class="modal-whats"></div>
                        </div>
                        <div class="col-10 text-center">
                            Eduardo Ramirez<br />
                            <span class="sub-box"> +57 3174420109 </span>
                        </div>
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
            showModal: false // Estado inicial del modal
        };
    },
    methods: {
        toggleModal(state) {
            this.showModal = state; // Alterna el estado del modal
        }
    }
};
</script>

<style>
/* whats */

.row-wha {
  padding: 0px 12px;
}

.div-what {
  position: fixed;
  width: 5%;
  height: 10%;
  bottom: 90px;
  right: 40px;
}

.header-wha {
  height: 85px;
  padding: 15px 15px;
  background-color: #2db742 !important;
  color: #fff !important;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
  line-height: 12px;
}

.sub-header {
  text-align: center !important;
  font-size: 11px;
  color: #fff;
  font-weight: 400;
}

.title-header {
  font-size: 15px;
  font-weight: bold;
  text-align: center;
}

.header-row {
  display: flex;
  align-items: center;
}

.box-wha {
  height: 57px;
  padding: 10px 15px;
  color: #000 !important;
  margin-bottom: 3px;
}

.box-wha-final {
  height: 57px;
  padding: 10px 15px;
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
  color: #000 !important;
}

.sub-box {
  text-align: center !important;
  font-size: 11px;
  color: #837f7f;
}

.sub-boxtitle {
  font-size: 9px;
  color: #837f7f;
}

.boxtitle {
  text-align: left !important;
  padding: 7px 10px;
}

.items-box {
  display: flex;
  align-items: center;
  line-height: 15px;
  height: 48px;
  background: #f3f3f3;
  margin: 0 -6px;
  border-radius: px;
  box-shadow: rgb(50 50 93 / 25%) 0px 2px 5px -1px, rgb(0 0 0 / 30%) 0px 1px 3px -1px;
}

.items-box:hover {
  -webkit-transform: scale(1.1);
  transform: scale(1.1);
  box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px, rgba(0, 0, 0, 0.22) 0px 10px 10px;
}

.float {
  position: fixed;
  background-size: contain;
  width: 58px;
  height: 58px;
  bottom: 35px;
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
  width: 165px;
  height: 43px;
  bottom: 44px;
  background-color: #fff;
  background-repeat: no-repeat;
  right: 109px;
  border-radius: 5px;
  text-align: left;
  box-shadow: rgb(0 0 0 / 25%) 0px 14px 28px, rgb(0 0 0 / 22%) 0px 10px 10px;
  z-index: 10000;
  padding: 10px 19px;
}

.float-message:focus {
  position: fixed;
  background-size: contain;
  width: 62px;
  height: 62px;
  bottom: 35px;
  background-repeat: no-repeat;
  right: 40px;
  border-radius: 50px;
  text-align: center;
  box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px, rgba(0, 0, 0, 0.22) 0px 10px 10px;
  z-index: 10000;
}

.float:focus {
  position: fixed;
  background-size: contain;
  width: 62px;
  height: 62px;
  bottom: 35px;
  background-repeat: no-repeat;
  right: 40px;
  background-image: url('/public/images/WhatsappIcon.png');
  border-radius: 50px;
  text-align: center;
  box-shadow: rgba(0, 0, 0, 0.25) 0px 14px 28px, rgba(0, 0, 0, 0.22) 0px 10px 10px;
  z-index: 10000;
}

.response {
  position: fixed;
  width: 25%;
  height: 145px;
  bottom: 115px;
  right: 40px;
  background-color: #fff;
  color: #000;
  border-radius: 8px;
  text-align: center;
  font-size: 13px;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;
  z-index: 10000;
}

.modal-whats {
  background-size: contain;
  width: 32px;
  height: 32px;
  background-repeat: no-repeat;
  background-image: url('/public/images/WhatsappIcon.png');
}

.header-logo {
  background-size: contain;
  margin: auto;
  display: block;
  width: 100px;
  height: 50px;
  background-repeat: no-repeat;
  background-image: url('/public/images/AlorangesWhite2.png');
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
    bottom: 60px;
  }

  .float:focus {
    right: 35px;
    bottom: 60px;
  }
  .float-message {
    bottom: 65px;
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

  .sub-box {
    font-size: 12px;
  }

  .sub-header {
    font-size: 12px;
  }
}
</style>
