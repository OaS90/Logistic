<template>
    <div>
        <button type="button" class="btn btn-outline-success" @click="save" :disabled='this.guest'>Сохранить</button>
        <button type="button" class="btn btn btn-info" @click="excel" :disabled='this.guest'>Выгрузить Excel</button>
        <input type="text" class="form-control col-4 filter" v-model="filter" placeholder="Поиск...">
        <div class="wmd-view-topscroll" :style="{'width': scrollWidth + 'px'}" @scroll="topScroll" ref="scroll">
            <div class="scroll-div1" :style="{'width': tableWidth + 'px'}">
            </div>
        </div>
        <div id="table-wrapper" ref="wrapper" @scroll="mainScroll">
            <table id="quotes" class="table-content">
                <tbody ref="table">
                <tr v-for="(item, id) in filteredRows" align="center" :key="`division-${id}`">
                    <td class="choose">
                        <input type="checkbox" v-model="item.to_save" @change="quoteToSave(item, id)">
                    </td>
                    <td>{{ item.filial_code }}</td>
                    <td class="p-2">{{ item.filial_name }}</td>
                    <td class="p-2">{{ item.region_name }}</td>
                    <td v-if="showQuoteProperties"></td>
                    <td></td>
                    <td></td>
                    <td v-if="!showSiteProperties && !showQuoteProperties"></td>
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
                    <td v-if="showSiteProperties">
                        <date-picker format="H:mm"
                                     class="date-time"
                                     v-model="item.in_day_limitation"
                                     type="time"
                                     name="delivery_till"
                                     value-type="H:mm"
                                     :disabled="!item.inDay"
                                     :timePickerOptions="{
                                        start: '00:00',
                                        step: '01:00',
                                        end: '23:00',
                                     }">
                        </date-picker>
                    </td>
                    <td v-if="showSiteProperties"><input type="checkbox" v-model="item.inHour"></td>
                    <td v-if="showSiteProperties">
                        <input type="text" v-model="item.deliveryDaysFromMoscow"
                               class="form-control"
                               @keypress="onlyNumber"
                               maxlength=2>
                    </td>
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
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[1].zone_a"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[2].zone_a"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[3].zone_a"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[4].zone_a"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[5].zone_a"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[6].zone_a"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[7].zone_a"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[1].zone_b"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[2].zone_b"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[3].zone_b"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[4].zone_b"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[5].zone_b"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[6].zone_b"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[7].zone_b"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[1].zone_c"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[2].zone_c"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[3].zone_c"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[4].zone_c"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[5].zone_c"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[6].zone_c"></td>
                    <td v-if="showZonesProperties"><input type="checkbox" v-model="item.days[7].zone_c"></td>
                </tr>
                </tbody>
                <thead>
                <tr align="center">
                    <th class="choose-th">Выбрать</th>
                    <th>Код филиала</th>
                    <th class="store">Филиал</th>
                    <th class="regions">Регион</th>
                    <th class="show-rows quote-settings" @click="showQutes()">Квоты <br> <i
                        :class="[showQuoteProperties ? 'la-angle-up' : 'la-angle-down', 'las']"></i></th>
                    <th class="show-rows site-settings" @click="showSite()">Настройки для сайта <br> <i
                        :class="[showSiteProperties ? 'la-angle-up' : 'la-angle-down', 'las']"></i></th>
                    <th class="show-rows zone-settings" @click="showZones()">Зоны <br> <i
                        :class="[showZonesProperties ? 'la-angle-up' : 'la-angle-down', 'las']"></i></th>
                    <th class="quote" v-if="showQuoteProperties">Дневная <br> квота</th>
                    <th class="tmp_quote" v-if="showQuoteProperties">Временная <br> квота</th>
                    <th class="tmp_period" v-if="showQuoteProperties">Срок <br> действия</th>
                    <th colspan="2" class="percent" v-if="showQuoteProperties">10-14</th>
                    <th colspan="2" class="percent" v-if="showQuoteProperties">14-18</th>
                    <th colspan="2" class="percent" v-if="showQuoteProperties">18-22</th>
                    <th v-if="showSiteProperties">День в день</th>
                    <th v-if="showSiteProperties">Ограничение ДвД</th>
                    <th v-if="showSiteProperties">Доставка<br>
                        в указанный час
                    </th>
                    <th v-if="showSiteProperties" class="deliveryFromMoscow">
                        Кол-во дней <br> доставки <br>со склада отгрузки
                    </th>
                    <th v-if="showSiteProperties">Ограничение по <br> времени оформления</th>
                    <th v-if="showSiteProperties">Часы доставки</th>
                    <th v-if="showSiteProperties">Блокировка Заказов</th>
                    <th v-if="showZonesProperties" colspan="7">Зона доставка А</th>
                    <th v-if="showZonesProperties" colspan="7">Зона доставка B</th>
                    <th v-if="showZonesProperties" colspan="7">Зона доставка C</th>
                </tr>
                <tr align="center">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th v-if="!showQuoteProperties"></th>
                    <th v-if="!showQuoteProperties"></th>
                    <th v-if="showQuoteProperties"></th>
                    <th v-if="showQuoteProperties"></th>
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
                    <th v-if="showSiteProperties"></th>
                    <th v-if="showSiteProperties"></th>
                    <th v-if="showSiteProperties"></th>
                    <th v-if="showSiteProperties"></th>
                    <th v-if="showSiteProperties"></th>
                    <th v-if="showZonesProperties">Пн</th>
                    <th v-if="showZonesProperties">Вт</th>
                    <th v-if="showZonesProperties">Ср</th>
                    <th v-if="showZonesProperties">Чт</th>
                    <th v-if="showZonesProperties">Пт</th>
                    <th v-if="showZonesProperties">Сб</th>
                    <th v-if="showZonesProperties">Вс</th>
                    <th v-if="showZonesProperties">Пн</th>
                    <th v-if="showZonesProperties">Вт</th>
                    <th v-if="showZonesProperties">Ср</th>
                    <th v-if="showZonesProperties">Чт</th>
                    <th v-if="showZonesProperties">Пт</th>
                    <th v-if="showZonesProperties">Сб</th>
                    <th v-if="showZonesProperties">Вс</th>
                    <th v-if="showZonesProperties">Пн</th>
                    <th v-if="showZonesProperties">Вт</th>
                    <th v-if="showZonesProperties">Ср</th>
                    <th v-if="showZonesProperties">Чт</th>
                    <th v-if="showZonesProperties">Пт</th>
                    <th v-if="showZonesProperties">Сб</th>
                    <th v-if="showZonesProperties">Вс</th>
                </tr>
                </thead>
            </table>
        </div>

        <modal v-if="showModal" @close="showModal = false">
            <template #body>
                <template v-if="loading">
                    {{ modalText }}
                    <PulseLoader :loading="loading" :color="'#7c69ef'"/>
                </template>
                <template v-else>
                    {{ modalText }}
                    <br>
                    <button class="btn btn-secondary" @click="showModal = false">ОК</button>
                </template>
            </template>
        </modal>
    </div>
</template>

<script>
import { PulseLoader } from "vue3-spinner"
import Modal from "./Modal.vue";
import DatePicker from "vue-datepicker-next";

export default {
    name: "QuotesTable",
    components: {Modal, PulseLoader, DatePicker},
    props: ['quotes', 'guest'],
    data() {
        return {
            quotesToSave: [],
            showModal: false,
            modalText: '',
            dataQuotes: this.quotes,
            loading: true,
            loadModal: false,
            filter: '',
            pageSize: 10,
            currentPage: 1,
            showQuoteProperties: false,
            showSiteProperties: false,
            showZonesProperties: false,
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
                const filialName = quote.filial_name.toLowerCase();
                const searchTerm = this.filter.toLowerCase();
                // let start = (this.currentPage - 1) * this.pageSize;
                // let end = this.currentPage * this.pageSize;

                if (this.filter !== '') return filialName.includes(searchTerm)
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
                this.loading = false
                this.showModal = !this.showModal
                this.modalText = 'Не выбрано ни одного филиала для обновления'
            } else {
                this.showModal = true
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
                    this.loading = false
                    this.showModal = true
                    this.modalText = response.data.message
                }).catch(errors => {
                    this.showModal = true
                    this.loading = false
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
        showZones() {
            this.showZonesProperties = !this.showZonesProperties
            setTimeout(() => this.tableWidth = this.$refs.table.clientWidth, 100);
        },
        topScroll(e) {
            let currentScrollPosition = e.srcElement.scrollLeft
            this.$refs.wrapper.scrollTo(currentScrollPosition, 0)
        },
        mainScroll(e) {
            let currentScrollPosition = e.srcElement.scrollLeft
            this.$refs.scroll.scrollTo(currentScrollPosition, 0)
        }
    },
}
</script>

<style scoped>
#quotes thead {
    background-color: #136d99;
}

#quotes {
    background-color: #4fa7d7;
    color: white;
    white-space: nowrap;
}
#table-wrapper {
    margin-top: 15px;
    overflow-x: scroll;
}
.wmd-view-topscroll {
    overflow-x: scroll;
    overflow-y: hidden;
}
.wmd-view-topscroll { height: 20px; }
.scroll-div1 {
    /*width: 1000px;*/
     overflow-x: scroll;
    /* overflow-y: hidden; */
}
#table-wrapper table thead th {
    top: -20px;
    z-index: 2;
    height: 20px;
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
