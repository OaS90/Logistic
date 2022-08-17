<template>

    <div>
        <button type="button" class="btn btn-outline-success" @click="save">Сохранить</button>
        <button type="button" class="btn btn btn-info" @click="excel">Выгрузить Excel</button>
        <input type="text" class="form-control col-4 filter" v-model="filter" placeholder="Поиск...">
        <table id="quotes" >
            <thead>
            <tr align="center">
                <th>Выбрать</th>
                <th class="store">Склад</th>
                <th class="regions">Регионы России</th>
                <th class="quote">Дневная квота</th>
                <th class="tmp_quote">Временная квота</th>
                <th class="tmp_period">Срок действия</th>
                <th colspan="2" class="percent">10-14</th>
                <th colspan="2" class="percent">14-18</th>
                <th colspan="2" class="percent">18-22</th>
                <th>День в день</th>
                <th>Доставка в указанный час</th>
                <th colspan="8"></th>
            </tr>
            <tr align="center">
                <th></th>
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
                <th>Пн</th>
                <th>Вт</th>
                <th>Ср</th>
                <th>Чт</th>
                <th>Пт</th>
                <th>Сб</th>
                <th>Вс</th>
                <th>Ограничение по времени оформления</th>
            </tr>
            </thead>
            <tbody>
                <tr v-for="(item, id) in filteredRows" align="center" :key="`division-${id}`">
                    <td><input type="checkbox" v-model="item.to_save" @change="quoteToSave(item, id)"></td>
                    <td>{{ item.warehouse }}</td>
                    <td>{{ item.division }}</td>
                    <td><input type="text" class="form-control" v-model="item.quote" @keypress="onlyNumber"></td>
                    <td><input type="text" class="form-control" v-model="item.tmp_quote" @keypress="onlyNumber"></td>
                    <td><date-picker range type="date" v-model="item.tmp_date" format="MM.DD.YYYY"></date-picker></td>
                    <td><input type="text" class="form-control" v-model="item.periodTenTwo.percent" @keypress="onlyNumber" maxlength=3></td>
                    <td><input type="checkbox" v-model="item.periodTenTwo.active"></td>
                    <td><input type="text" class="form-control" v-model="item.periodTwoSix.percent" @keypress="onlyNumber" maxlength=3></td>
                    <td><input type="checkbox" v-model="item.periodTwoSix.active"></td>
                    <td><input type="text" class="form-control" v-model="item.periodSixTen.percent" @keypress="onlyNumber" maxlength=3></td>
                    <td><input type="checkbox" v-model="item.periodSixTen.active"></td>
                    <td><input type="checkbox" v-model="item.inDay"></td>
                    <td><input type="checkbox" v-model="item.inHour"></td>
                    <td><input type="checkbox" v-model="item.days[1]"></td>
                    <td><input type="checkbox" v-model="item.days[2]"></td>
                    <td><input type="checkbox" v-model="item.days[3]"></td>
                    <td><input type="checkbox" v-model="item.days[4]"></td>
                    <td><input type="checkbox" v-model="item.days[5]"></td>
                    <td><input type="checkbox" v-model="item.days[6]"></td>
                    <td><input type="checkbox" v-model="item.days[7]"></td>
                    <td><date-picker format="H:mm"
                                     class="date-time"
                                     v-model="item.time_last"
                                     type="time"
                                     name="delivery_till"
                                     value-type="H:mm"
                                     :timePickerOptions="{
                                        start: '00:00',
                                        step: '01:00',
                                        end: '23:00',
                                     }">
                        </date-picker>
                    </td>
                </tr>
            </tbody>
        </table>
<!--        <p>-->
<!--            <button type="button" class="btn btn-secondary" @click="prevPage">Предыдущая</button>-->
<!--            <button type="button" class="btn btn-secondary" @click="nextPage">Следующая</button>-->
<!--            <span style="padding-left: 5px">{{ currentPage }} из {{ totalPage }}</span>-->
<!--        </p>-->

        <modal v-if="showModal" @close="showModal = false">
            <span slot="body">{{ modalText }}</span>
            <span slot="footer"></span>
        </modal>
    </div>
</template>

<script>
import DatePicker from "vue2-datepicker";
import Modal from "./Modal";

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
                const division = quote.warehouse.toLowerCase();
                const searchTerm = this.filter.toLowerCase();
                // let start = (this.currentPage - 1) * this.pageSize;
                // let end = this.currentPage * this.pageSize;

                if (this.filter !== '') return division.includes(searchTerm)
                // if (index >= start && index < end)
                return true
            });
        },
        // totalPage() {
        //     return Math.ceil(this.dataQuotes.length / this.pageSize)
        // }
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
                this.showModal = !this.showModal
                this.modalText = 'Не выбрано ни одного филиала для обновления'
            } else {
                var date = new Date()

                this.quotesToSave.forEach(item => {
                    if (item.tmp_date && item.tmp_date.length > 0) {
                        // console.log(new Date(item.tmp_date[0].toString()).toISOString().slice(0,10))
                        item.tmp_date[0] = new Date(item.tmp_date[0]).toDateString()
                        item.tmp_date[1] = new Date(item.tmp_date[1]).toDateString()
                    }
                })

                axios.post('save-quotes', this.quotesToSave).then(response => {
                    this.showModal = !this.showModal
                    this.modalText = response.data.message
                }).catch(errors => {
                    this.showModal = !this.showModal
                    this.modalText = errors.response.data.message
                })
            }
        },
        excel() {
            axios.get('download-excel', { responseType: 'blob' }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'quotes.xlsx');
                document.body.appendChild(link);
                link.click();
            })
        },
        // nextPage() {
        //     if((this.currentPage * this.pageSize) < this.dataQuotes.length) this.currentPage++;
        // },
        // prevPage() {
        //     if(this.currentPage > 1) this.currentPage--;
        // },
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
    #quotes thead {
        background-color: #136d99;
    }
    #quotes {
        margin-top: 15px;
        background-color: #4fa7d7;
        color: white;
    }
    .filter {
        margin-top: 15px;
    }

    input[type=checkbox] {
        transform: scale(2);
    }

    #quotes {
        width: 100%;
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
    #quotes td {
        border: 1px solid #ddd;
    }
    #quotes th {
        border: 1px solid #ddd;
        padding: 15px;
    }
    th.store {
        width: 20%
    }
    th.regions {
        width: 20%
    }
    th.quote {
        width: 5%;
    }
    th.tmp_quote {
        width: 5%;
    }
    th.tmp_period {
        width:8%;
    }
    th.percent {
        width: 20%;
    }
    .mx-datepicker-range {
        width:280px !important;
    }
    input.time-picker {
        display: block;
        width: 100%;
        height: 50px;
        padding: 0 16px;
        transition: border-color .15s ease-in-out;
        color: #464b51;
        background: #fff;
        border: 1px solid #ced8e1;
        border-radius: 8px;
        outline: 0 none;
        margin-bottom: 30px;
    }
</style>
