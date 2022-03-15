<template>
    <div class="modal" :class="{active: show}">
        <svg @click="close" class="modal__cross js-modal-close" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <path d="M11.9997 10.586L16.9497 5.63599L18.3637 7.04999L13.4137 12L18.3637 16.95L16.9497 18.364L11.9997 13.414L7.04974 18.364L5.63574 16.95L10.5857 12L5.63574 7.04999L7.04974 5.63599L11.9997 10.586Z" fill="#888888"/>
        </svg>
        <div class="modal-title">Спасибо за заявку!</div>
        <div class="modal-text">Отслеживать статус или редактировать заявку,
            вы можете в разделе <a href="/application-list">«Список заявок»</a></div>
        <div class="btn request-btn">
            <span @click="close">Создать еще заявку</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "Popup",
    props: ['show'],
    methods: {
        close() {
            let close = this.show
            close = false
            this.$emit('handlerClose', close)
        }
    }
}
</script>

<style scoped>

</style>
