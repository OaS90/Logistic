<template>

    <div>
        <button type="button" class="btn btn-outline-success" @click="save">Сохранить</button>
        <input type="text" class="form-control col-4 filter" v-model="filter" placeholder="Поиск...">
        <table class="table table-bordered" id="quotes">
            <thead>
            <tr align="center">
                <th>Выбрать</th>
                <th>Филиал</th>
                <th>Дневная квота</th>
                <th>Временная квота</th>
                <th>Срок действия</th>
                <th colspan="2">10-14</th>
                <th colspan="2">14-18</th>
                <th colspan="2">18-22</th>
                <th>День в день</th>
                <th>Доставка в указанный час</th>
            </tr>
            <tr align="center">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>%</th>
                <th>Активно</th>
                <th>%</th>
                <th>Активно</th>
                <th>%</th>
                <th>Активно</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="(item, id) in filteredRows" align="center" :key="`division-${id}`">
                    <td><input type="checkbox" v-model="item.to_save" @change="quoteToSave(item, id)"></td>
                    <td>{{ item.division }}</td>
                    <td><input type="text" class="form-control" v-model="item.quote" @keypress="onlyNumber"></td>
                    <td><input type="text" class="form-control" v-model="item.tmp_quote" @keypress="onlyNumber"></td>
                    <td><date-picker range type="date" v-model="item.tmp_date"></date-picker></td>
                    <td><input type="text" class="form-control" v-model="item.periodTenTwo.percent" @keypress="onlyNumber"></td>
                    <td><input type="checkbox" v-model="item.periodTenTwo.active"></td>
                    <td><input type="text" class="form-control" v-model="item.periodTwoSix.percent" @keypress="onlyNumber"></td>
                    <td><input type="checkbox" v-model="item.periodTwoSix.active"></td>
                    <td><input type="text" class="form-control" v-model="item.periodSixTen.percent" @keypress="onlyNumber"></td>
                    <td><input type="checkbox" v-model="item.periodSixTen.active"></td>
                    <td><input type="checkbox" v-model="item.inDay"></td>
                    <td><input type="checkbox" v-model="item.inHour"></td>
                </tr>
            </tbody>
        </table>
        <p>
            <button type="button" class="btn btn-secondary" @click="prevPage">Предыдущая</button>
            <button type="button" class="btn btn-secondary" @click="nextPage">Следующая</button>
        </p>

        <modal :show="showModal">
            <svg @click="closeModal('csv')" class="modal__cross js-modal-close" fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M11.9997 10.586L16.9497 5.63599L18.3637 7.04999L13.4137 12L18.3637 16.95L16.9497 18.364L11.9997 13.414L7.04974 18.364L5.63574 16.95L10.5857 12L5.63574 7.04999L7.04974 5.63599L11.9997 10.586Z" fill="#888888"/>
            </svg>
            <h3>{{ modalText }}</h3>
        </modal>
    </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
import Modal from "../Popup";

export default {
    name: "QuotesTable",
    props: ['quotes'],
    data () {
        return {
            quotesToSave: [],
            showModal: false,
            modalText: '',
            dataQuotes: this.quotes,
            filter: '',
            pageSize: 10,
            currentPage: 1
        }
    },
    computed: {
        filteredRows() {
            return this.dataQuotes.filter((quote, index) => {
                const division = quote.division.toLowerCase();
                const searchTerm = this.filter.toLowerCase();
                let start = (this.currentPage - 1) * this.pageSize;
                let end = this.currentPage * this.pageSize;

                if (this.filter !== '') return division.includes(searchTerm)
                if (index >= start && index < end) return true;
            });
        }
    },
    beforeMount() {
        // парсим даты для того, чтобы нормально отображались в datepickere
        for (let i = 0; i < this.dataQuotes.length; i++) {
            var item = this.dataQuotes[i];

            if (item.tmp_date && item.tmp_date.length > 0) {
                item.tmp_date[0] = new Date(item.tmp_date[0])
                item.tmp_date[1] = new Date(item.tmp_date[1])
            }
        }
    },
    methods: {
        save() {
            if (this.quotesToSave.length === 0) {
                this.showModal = true
                this.modalText = 'Не выбрано ни одного филиала для обновления'
            } else {
                axios.post('save-quotes', this.quotesToSave).then(response => {
                    console.log(response)
                }).catch()
            }
        },
        nextPage() {
            if((this.currentPage*this.pageSize) < this.dataQuotes.length) this.currentPage++;
        },
        prevPage() {
            if(this.currentPage > 1) this.currentPage--;
        },
        quoteToSave(quote) {
            if (quote.to_save) {
                this.quotesToSave.push(quote)
            }
        },
        closeModal() {
            this.showModal = false
        },
        onlyNumber ($event) {
            //console.log($event.keyCode); //keyCodes value
            let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
                $event.preventDefault();
            }
        }
    },
    components: {
        DatePicker,
        Modal
    }
}
</script>

<style scoped>
    #quotes {
        margin-top: 15px;
    }
    .filter {
        margin-top: 15px;
    }
</style>
