<template>
    <transition name="modal">
        <div class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <div class="modal-body">
                        <slot name="body">
                            default body
                        </slot>
                        <br>
                        <button class="btn btn-secondary" @click="$emit('close')">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: "Modal",
    mounted () {
        $("body").addClass("modal-open")
    },

    beforeDestroy() {
        $("body").removeClass("modal-open")
    }
}
</script>

<style scoped>
    .btn {
        margin-top: 10px;
        width: 30%;
    }
    .modal-container {
        border-radius: 11px;
        padding: 10px;
        width: fit-content;
        min-width: 300px;
        text-align: center;
    }
</style>
