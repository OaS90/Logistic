<template>
    <div>
        <div class="ml-5 mb-4">
            <button class="btn btn-success" @click="save">Сохранить</button>
            <button class="btn btn-primary" @click="getAll">Выбрать все</button>
            <button class="btn btn-secondary" @click="add">Добавить</button>
        </div>

        <table class="table table-bordered w-50 ml-5" style="text-align: center">
            <tr>
                <td>Email</td>
                <td>Активность</td>
                <td></td>
            </tr>

            <tr v-if="newRows.length > 0" v-for="(row, index) in newRows" :id="index">
                <td class="email">
                    <input class="form-control" type="text" v-model="newRows[index].email">
                </td>
                <td class="active"><input type="checkbox" v-model="newRows[index].active" @click="activeEmail(index, 'new')"></td>
                <td><button class="btn btn-outline-danger ml-3" @click="deleteNewRow(index)">Удалить</button></td>
            </tr>
            <tr v-for="(email, index) in emailsArr" :key="index" :id="index">
                <td class="email">{{ email.email }}</td>
                <td class="active">
                    <input type="checkbox"
                           v-model="selected.length > 0 ? selected : email.active"
                           :value="email.id"
                           @click="activeEmail(index)">
                </td>
                <td><button class="btn btn-danger" @click="deleteEmail(email.id, index)">Удалить</button></td>
            </tr>
        </table>

        <modal v-if="showModal" @close="showModal = false">
            <span slot="body">{{ modalText }}</span>
            <span slot="footer"></span>
        </modal>
    </div>
</template>

<script>
import Modal from "./Modal";

export default {
    name: "QuoteEmailsTable",
    props: ['emails'],
    data() {
        return {
            selected: [],
            newRows: [],
            showModal: false,
            modalText: '',
            emailsArr: this.emails
        }
    },
    methods: {
        getAll() {
            var selected = [];

            if (this.selected.length === 0) {
                this.emails.forEach(email => {
                    selected.push(email.id)
                    email.active = true
                })
            } else {
                this.emails.forEach(email => {
                    email.active = false
                })
            }

            this.selected = selected
        },
        activeEmail(index, type) {

            if (type != 'new') {
                var email = null
                email = this.emails[index]
                email.active = !email.active
            } else {
                var newEmail = this.newRows[index]
                newEmail.active = !newEmail.active
            }
        },
        save() {
            this.newRows.forEach(row => {
                this.emails[this.emails.length - 1] = row
            })

            axios.post('save-quote-emails', this.emails).then(response => {
                this.showModal = !this.showModal
                this.modalText = 'Данные сохранены!'
            }).catch(errors => {
                this.showModal = !this.showModal
                this.modalText = 'Ошибка сохранения!'
            })
        },
        add() {
            var lastIndex = this.emails.length - 1
            this.newRows.splice(lastIndex + 1, 0, {id: lastIndex + 2, active: false});
        },
        deleteNewRow(index) {
            this.$delete(this.newRows, index)
        },
        deleteEmail(id, index) {
            this.$delete(this.emailsArr, index)
            axios.post('delete-quote-email/' + id, )
        }
    },
    components: {
        Modal
    }
}
</script>

<style scoped>
input[type=checkbox] {
    transform: scale(2);
}
</style>
