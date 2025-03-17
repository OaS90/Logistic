<template>
    <form @submit.prevent="submit">
        <div class="input-wrap">
            <input type="text" id="surname" name="lastname" class="text" placeholder="Фамилия" v-model="fields.lastname">
            <span class="error" v-if="errors.lastname">{{ errors['lastname'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input type="text" id="name" name="firstname" class="text" placeholder="Имя" v-model="fields.firstname">
            <span class="error" v-if="errors.firstname">{{ errors['firstname'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input type="text" id="mname" name="patronymic" class="text" placeholder="Отчество" v-model="fields.patronymic">
            <span class="error" v-if="errors.patronymic">{{ errors['patronymic'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input type="text" id="position" name="position" class="text" placeholder="Должность" v-model="fields.position">
            <span class="error" v-if="errors.position">{{ errors['position'][0] }}</span>
        </div>
        <div class="field-group input-wrap">
            <input type="text" id="wphone" name="work_phone" class="text text--middle"
                   placeholder="Рабочий телефон"  v-model="fields.work_phone">
            <input type="text" id="dob" name="additional_number" class="text text--small" placeholder="доб.">
            <span class="error" v-if="errors.work_phone">{{ errors['work_phone'][0] }}</span>
        </div>
        <div class="field-group input-wrap" v-for="(input, k) in fields.warehouses" :key="k">
            <input type="text" name="warehouse" class="text text--middle"
                   placeholder="Адрес склада"  v-model="fields.warehouses[k].address">
            <span @click="addWarehouse(k)" v-show="k == fields.warehouses.length - 1"
                class="add_warehouse">Добавить склад <b>+</b></span>
        </div>
        <div class="input-wrap">
            <input type="text" id="timezone" name="timezone" class="text" placeholder="Часовой пояс" v-model="fields.timezone">
            <span class="error" v-if="errors.timezone">{{ errors['timezone'][0] }}</span>
        </div>
        <div class="input-wrap">
<!--            <input type="text" id="sms-mobile" class="text" name="mobile_phone"-->
<!--                   placeholder="Мобильный телефон для SMS-оповещений" v-model="fields.mobile_phone">-->
            <MaskInput mask="+7 (###) ###-##-##" class="text" placeholder="Мобильный телефон для SMS-оповещений"
                          v-model="fields.mobile_phone"  autocomplete="tel-national"></MaskInput>
            <span class="error" v-if="errors.mobile_phone">{{ errors['mobile_phone'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input type="text" id="email" class="text" placeholder="E-mail" name="email" v-model="fields.email">
            <span class="error" v-if="errors.email">{{ errors['email'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input type="text" id="company1" class="text" placeholder="Компания" name="company" v-model="fields.company">
            <span class="error" v-if="errors.company">{{ errors['company'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input type="text" id="inn" class="text" placeholder="ИНН" name="inn" v-model="fields.inn">
            <span class="error" v-if="errors.inn">{{ errors['inn'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input type="text" id="kpp" class="text" placeholder="КПП" name="kpp" v-model="fields.kpp">
            <span class="error" v-if="errors.kpp">{{ errors['kpp'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input type="text" id="okpo" class="text" placeholder="ОКПО" name="okpo" v-model="fields.okpo">
            <span class="error" v-if="errors.okpo">{{ errors['okpo'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input type="text" id="leg-address" class="text" placeholder="Юридический адрес"
                   name="legal_address" v-model="fields.legal_address">
            <span class="error" v-if="errors.legal_address">{{ errors['legal_address'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input id="password" type="password" class="text" name="password" autocomplete="new-password"
                   placeholder="Пароль" v-model="fields.password">
            <span class="error" v-if="errors.password">{{ errors['password'][0] }}</span>
        </div>
        <div class="input-wrap">
            <input id="password-confirm" type="password" class="text" name="password_confirmation"
                   placeholder="Подтвердите пароль" v-model="fields.password_confirmation">
            <span class="error" v-if="errors.password_confirmation">{{ errors['password_confirmation'][0] }}</span>
        </div>
        <div class="row mb-0">
            <div class="col-md-6 offset-md-4">
                <button class="btn btn-primary">
                    Зарегистрировать
                </button>
            </div>
        </div>
    </form>
</template>

<script>

import {MaskInput} from "vue-3-mask";

export default {
    name: "RegisterForm",
    data() {
        return {
            fields: {
                warehouses: [{ address: '',}]
            },
            errors: [],
        }
    },
    methods: {
        submit() {
            this.errors = []
            axios.post('register', this.fields).then((response) => {
                window.location = '/profile'
            }).catch((err) => {
                this.errors = err.response.data.errors
            })
        },
        addWarehouse() {
            this.fields.warehouses.push({
                address: '',
            })
        }
    },
    components: {
        MaskInput
    }
}
</script>

<style scoped>
.add_warehouse b {
    font-size: 20px;

}
.add_warehouse:hover {
    cursor: pointer;
}
</style>
