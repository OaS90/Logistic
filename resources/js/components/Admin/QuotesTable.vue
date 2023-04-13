<template>
    <div>
        <button type="button" class="btn btn-outline-success" @click="save">Сохранить</button>
        <button type="button" class="btn btn btn-info" @click="excel">Выгрузить Excel</button>
        <input type="text" class="form-control col-4 filter" v-model="filter" placeholder="Поиск...">
        <div id="table-wrapper" ref="wrapper">
            <div class="wmd-view-topscroll" :style="{'width': scrollWidth + 'px'}" @scroll="topScroll" ref="scroll">
                <div class="scroll-div1" :style="{'width': tableWidth + 'px'}">
                </div>
            </div>
            <table id="quotes" class="table-content" ref="maintable" @scroll="mainScroll">
                <tbody ref="table">
                <tr v-for="(item, id) in filteredRows" align="center" :key="`division-${id}`">
                    <td class="choose"><input type="checkbox" v-model="item.to_save"
                                              @change="quoteToSave(item, id)"></td>
                    <td>{{ item.warehouse }}</td>
                    <td>{{ item.division }}</td>
                    <td v-if="showQuoteProperties"></td>
                    <td></td>
                    <td v-if="showSiteProperties && !showQuoteProperties"></td>
                    <td v-if="showQuoteProperties"><input type="text" class="form-control" v-model="item.quote"
                                                          @keypress="onlyNumber"></td>
                    <td v-if="showQuoteProperties"><input type="text" class="form-control" v-model="item.tmp_quote"
                                                          @keypress="onlyNumber"></td>
                    <td v-if="showQuoteProperties">
                        <date-picker range type="date" v-model="item.tmp_date" format="MM.DD.YYYY"></date-picker>
                    </td>
                    <td v-if="showQuoteProperties"><input type="text" class="form-control"
                                                          v-model="item.periodTenTwo.percent"
                                                          @keypress="onlyNumber" maxlength=3></td>
                    <td v-if="showQuoteProperties"><input type="checkbox" v-model="item.periodTenTwo.active"></td>
                    <td v-if="showQuoteProperties"><input type="text" class="form-control"
                                                          v-model="item.periodTwoSix.percent"
                                                          @keypress="onlyNumber" maxlength=3></td>
                    <td v-if="showQuoteProperties"><input type="checkbox" v-model="item.periodTwoSix.active"></td>
                    <td v-if="showQuoteProperties"><input type="text" class="form-control"
                                                          v-model="item.periodSixTen.percent"
                                                          @keypress="onlyNumber" maxlength=3></td>
                    <td v-if="showQuoteProperties"><input type="checkbox" v-model="item.periodSixTen.active"></td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.inDay"></td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.inHour"></td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.days[1]"></td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.days[2]"></td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.days[3]"></td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.days[4]"></td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.days[5]"></td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.days[6]"></td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.days[7]"></td>
                    <td v-if="showSiteProperties"><input type="text" v-model="item.deliveryDaysFromMoscow"
                                                         @keypress="onlyNumber" maxlength=1></td>
                    <td v-if="showSiteProperties">
                        <date-picker format="H:mm"
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
                    <td v-if="showSiteProperties">
                        <span>c :</span>
                        <date-picker type="time"
                                     class="date-time"
                                     format="H:mm"
                                     value-type="H:mm"
                                     v-model="item.delivery_hours['from']"
                                     :timePickerOptions="{
                                        start: '00:00',
                                        step: '01:00',
                                        end: '23:00',
                                     }">
                        </date-picker>
                        <span>до :</span>
                        <date-picker type="time"
                                     class="date-time"
                                     v-model="item.delivery_hours['till']"
                                     format="H:mm"
                                     value-type="H:mm"
                                     :timePickerOptions="{
                                        start: '00:00',
                                        step: '01:00',
                                        end: '23:00',
                                     }">

                        </date-picker>
                    </td>
                    <td v-if="showSiteProperties">
                        <date-picker range type="date"
                                     v-model="item.blocked_dates"
                                     format="MM.DD.YYYY"
                        >
                        </date-picker>
                    </td>
                </tr>
                </tbody>
                <thead>
                <tr align="center">
                    <th>Выбрать</th>
                    <th class="store">Склад</th>
                    <th class="regions">Регионы России</th>
                    <th class="show-rows" @click="showQutes()">Квоты <br> <i
                        :class="[showQuoteProperties ? 'la-angle-up' : 'la-angle-down', 'las']"></i></th>
                    <th class="show-rows" @click="showSite()">Настройки для сайта <br> <i
                        :class="[showSiteProperties ? 'la-angle-up' : 'la-angle-down', 'las']"></i></th>
                    <th class="quote" v-if="showQuoteProperties">Дневная <br> квота</th>
                    <th class="tmp_quote" v-if="showQuoteProperties">Временная <br> квота</th>
                    <th class="tmp_period" v-if="showQuoteProperties">Срок <br> действия</th>
                    <th colspan="2" class="percent" v-if="showQuoteProperties">10-14</th>
                    <th colspan="2" class="percent" v-if="showQuoteProperties">14-18</th>
                    <th colspan="2" class="percent" v-if="showQuoteProperties">18-22</th>
                    <th v-if="showSiteProperties">День в день</th>
                    <th v-if="showSiteProperties">Доставка<br>
                        в указанный час
                    </th>
                    <th v-if="showSiteProperties" colspan="11"></th>
                </tr>
                <tr align="center">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th v-if="showQuoteProperties"></th>
                    <th v-if="showQuoteProperties"></th>
                    <th v-if="showQuoteProperties"></th>
                    <th v-if="showQuoteProperties">%</th>
                    <th v-if="showQuoteProperties">Активно</th>
                    <th v-if="showQuoteProperties">%</th>
                    <th v-if="showQuoteProperties">Активно</th>
                    <th v-if="showQuoteProperties">%</th>
                    <th v-if="showQuoteProperties">Активно</th>
<!--                    <th v-if="showQuoteProperties"></th>-->
<!--                    <th v-if="showQuoteProperties"></th>-->
                    <th v-if="showSiteProperties"></th>
                    <th v-if="showSiteProperties"></th>
                    <th v-if="showSiteProperties">Пн</th>
                    <th v-if="showSiteProperties">Вт</th>
                    <th v-if="showSiteProperties">Ср</th>
                    <th v-if="showSiteProperties">Чт</th>
                    <th v-if="showSiteProperties">Пт</th>
                    <th v-if="showSiteProperties">Сб</th>
                    <th v-if="showSiteProperties">Вс</th>
                    <th v-if="showSiteProperties" class="deliveryFromMoscow">Кол-во дней <br> доставки <br>из Москвы
                    </th>
                    <th v-if="showSiteProperties">Ограничение по <br> времени оформления</th>
                    <th v-if="showSiteProperties">Часы доставки</th>
                    <th v-if="showSiteProperties">Блокировка Заказов</th>
                </tr>
                </thead>
            </table>
        </div>
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
import Popup from "../Popup";

export default {
    name: "QuotesTable",
    props: ['quotes'],
    data() {
        return {
            quotesToSave: [],
            showModal: false,
            modalText: '',
            dataQuotes: this.quotes,
            filter: '',
            pageSize: 10,
            currentPage: 1,
            showQuoteProperties: false,
            showSiteProperties: false,
            scrollWidth: 0,
            tableWidth: 0,
            scrollPosition: 0
        }
    },
    mounted() {
        this.scrollWidth = this.$refs.wrapper.clientWidth
        this.tableWidth = this.$refs.table.clientWidth
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

            if (item.blocked_dates && item.blocked_dates.length > 0) {
                item.blocked_dates[0] = new Date(item.blocked_dates[0])
                item.blocked_dates[1] = new Date(item.blocked_dates[1])
            }
        }
    },
    methods: {
        save() {
            if (this.quotesToSave.length === 0) {
                this.showModal = !this.showModal
                this.modalText = 'Не выбрано ни одного филиала для обновления'
            } else {
                this.quotesToSave.forEach(item => {
                    if ((item.tmp_date && item.tmp_date.length > 0) &&
                        (item.tmp_date[0] && item.tmp_date[1])) {
                        // console.log(new Date(item.tmp_date[0].toString()).toISOString().slice(0,10))
                        item.tmp_date[0] = new Date(item.tmp_date[0]).toDateString()
                        item.tmp_date[1] = new Date(item.tmp_date[1]).toDateString()
                    }

                    if ((item.blocked_dates && item.blocked_dates.length > 0) &&
                        (item.blocked_dates[0] && item.blocked_dates[1])) {
                        // console.log(new Date(item.tmp_date[0].toString()).toISOString().slice(0,10))
                        item.blocked_dates[0] = new Date(item.blocked_dates[0]).toDateString()
                        item.blocked_dates[1] = new Date(item.blocked_dates[1]).toDateString()
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
            axios.get('download-excel', {responseType: 'blob'}).then((response) => {
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
        onlyNumber($event) {
            //console.log($event.keyCode); //keyCodes value
            let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
                $event.preventDefault();
            }
        },
        showQutes() {
            this.showQuoteProperties = !this.showQuoteProperties
            setTimeout(() => this.tableWidth = this.$refs.table.clientWidth, 100);
        },
        showSite() {
            this.showSiteProperties = !this.showSiteProperties
            setTimeout(() => this.tableWidth = this.$refs.table.clientWidth, 100);
        },
        topScroll(e) {
            let currentScrollPosition = e.srcElement.scrollLeft
            this.$refs.maintable.scrollTo(currentScrollPosition, 0)
        },
        mainScroll(e) {
            let currentScrollPosition = e.srcElement.scrollLeft
            this.$refs.scroll.scrollTo(currentScrollPosition, 0)
        }
    },
    components: {
        Popup,
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
    background-color: #4fa7d7;
    color: white;
    width: 100%;
    display: block;
    overflow-x: scroll;
    white-space: nowrap;
}
#table-wrapper {
    margin-top: 15px;
}
.wmd-view-topscroll {
    overflow-x: scroll;
    /* overflow-y: hidden; */
    width: 300px;
    border: none 0px RED;
}
.wmd-view-topscroll { height: 20px; }
.scroll-div1 {
    width: 1000px;
    /* overflow-x: scroll; */
    /* overflow-y: hidden; */
}
#table-wrapper table thead th {
    top: -20px;
    z-index: 2;
    height: 20px;
    width: 35%;
}

.filter {
    margin-top: 15px;
}

input[type=checkbox] {
    transform: scale(2);
}

#quotes td {
    border: 1px solid #ddd;
}

#quotes th {
    border: 1px solid #ddd;
    padding: 15px;
}

th.store {
    width: 12%
}

th.regions {
    width: 12%
}

th.quote {
    width: 3%;
}

th.tmp_quote {
    width: 2%;
}

th.tmp_period {
    width: 5%;
}

th.percent {
    width: 7%;
}

th.deliveryFromMoscow {
    width: 5%;
}

.mx-datepicker-range {
    width: 280px !important;
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

.date-time {
    width: 120px;
}

td span {
    padding: 0 5px;
}

.show-rows:hover {
    cursor: pointer;
}

.choose {
    padding: 10px;
}
</style>
