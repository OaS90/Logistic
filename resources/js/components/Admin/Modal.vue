<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-body">
                        <slot name="body">
<!--                            default body-->
<!--                            <br>-->
<!--                            <button :class="btnClass" @click="$emit('close')">{{ btnText }}</button>-->
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    props: ['buttonName', 'buttonClass'],
    data() {
        return {
            btnText: 'OK',
            btnClass: 'btn btn-secondary'
        }
    },
    name: "Modal",
    mounted () {
        $("body").addClass("modal-open")

        if (this.buttonName) {
            this.btnText = this.buttonName
        }

        if (this.buttonClass) {
            this.btnClass = this.buttonClass
        }
    },
    beforeUnmount() {
        $("body").removeClass("modal-open")
    },
}
</script>

<style scoped>
    .btn {
        margin-top: 10px;
        width: 30%;
        padding: 5px 0;
    }
    .modal-container {
        border-radius: 11px;
        padding: 10px;
        width: fit-content;
        min-width: 300px;
        text-align: center;
    }
    .modal-body {
        overflow-y: auto;
        max-height: 50vh;
    }
</style>
