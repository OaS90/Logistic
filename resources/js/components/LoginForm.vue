<template>
    <div>
        <h1>Добро пожаловать в&nbsp;личный кабинет</h1>
        <div class="h1">ООО «Транспорт Логистика»</div>
        <div class="login-form">
            <div class="login-form__wrapper">
                <h3>Вход в личный кабинет</h3>
        <form id="login_form" @submit.prevent="submit">
            <div class="login-form__container">
                <div class="error-block">
                    <p class="error-text" v-if="errors.email">Неверный логин или пароль</p>
                </div>

                <input type="text" value="" id="email" class="login-form__input"
                       placeholder="Телефон или email" name="email" v-model="fields.email">
                <input type="password" value="" id="password" name="password" class="login-form__input"
                       placeholder="Пароль" v-model="fields.password">
                <input type="submit" value="Войти" class="btn login-form__btn">
            </div>
            <div class="login-form_password">
                <div class="login-checkbox">
                    <input id="remember" type="checkbox" class="login-checkbox__input" name="remember" value="">
                    <label class="login-checkbox__label" for="remember">Запомнить меня</label>
                </div>
                <div class="forgot-password">Забыли пароль?</div>
            </div>
        </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "LoginForm",
    data() {
        return {
            errors: [],
            fields: {}
        }
    },
    methods: {
        submit() {
            this.errors = []
            axios.post('login', this.fields).then((response) => {
                window.location = '/profile'
            }).catch((err) => {
                this.errors = err.response.data.errors
            })
        }
    }
}
</script>

<style scoped>
    .error-block {
        display: inline-block;
        position: fixed;
        width: 25%;
    }
    .error-text {
        position: absolute;
        top: 40px;
        color: coral;
        font-weight: 600;
    }
</style>
