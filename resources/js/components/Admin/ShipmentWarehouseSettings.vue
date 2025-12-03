<script setup>
import {ref, watch, onMounted} from 'vue'

// Принимаем пропсы из Blade
const props = defineProps({
    initialValue: {
        type: Array,
    },
});

// Реактивное состояние (ваши параметры)
const settings = ref(props.initialValue);
console.log(settings)
const addParam = () => {
    settings.value.push({
        'key': '',
        'value': ''
    })
}
const deleteParam = (index) => {
    settings.value.splice(index, 1)
}
// Функция синхронизации с реальным инпутом формы
const updateHiddenInput = (newValue) => {
    const inputElement = document.getElementById('hiddenInput');

    if (inputElement) {
        inputElement.value = JSON.stringify(newValue)
    }
    // if (inputElement) {
    //     // Превращаем объект обратно в строку для отправки
    //     inputElement.value = JSON.stringify(newValue);
    //
    //     // Иногда полезно вызвать событие change, если другие скрипты следят за полем
    //     inputElement.dispatchEvent(new Event('change'));
    // }
};

// Следим за любыми изменениями в settings
watch(settings, (newValue) => {
    updateHiddenInput(newValue);
}, { deep: true });
</script>

<template>
    <div class="btn btn-primary" @click="addParam">Добавить параметр</div>

    <div v-if="settings.length > 0">
        <div v-for="(setting, index) in settings">
            <hr>
            <div class="form-group col-sm-12 mb-3">
                <label>Наименование параметра</label>
                <input type="text" class="form-control" v-model="setting.key">
            </div>
            <div class="form-group col-sm-12 mb-3">
                <label>Значение параметра</label>
                <input type="text" class="form-control" v-model="setting.value">
            </div>
            <div class="form-group col-sm-12 mb-3">
                <div class="btn btn-danger" @click="deleteParam(index)">Удалить</div>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
