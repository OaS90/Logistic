<template>
    <section class="request-section--manually">
        <h3>Создание заявки в ручную</h3>
        <div class="request-form">
            <div class="request-form__wrapper">
                <form id="request-form" name="request-form" method="post">
                    <input type="text" value="" id="n-order" class="text" placeholder="Номер заказа" name="order_number"
                           v-model="applicationFields.order_number">
                    <input type="text" value="" id="n-product" class="text" placeholder="Наименование товара" name="product_name"
                           v-model="productFields.name">
                    <input type="text" value="" id="vendor" class="text" placeholder="Артикул" name="product_art"
                           v-model="productFields.sku">
                    <input type="text" value="" id="brand" class="text" placeholder="Бренд" name="product_brand"
                           v-model="productFields.brand">
                    <div class="field-title">Форма оплаты</div>
                    <select class="select" name="payment_type" v-model="applicationFields.payment_type">
                        <option>Онлайн оплата банковской картой</option>
                        <option>Онлайн оплата</option>
                        <option>Онлайн оплата банковской картой</option>
                    </select>
                    <div class="field-title">Ставка НДС</div>
                    <select class="select" name="vat" v-model="productFields.vat">
                        <option value="0">0%</option>
                        <option value="10">10%</option>
                        <option value="20">20%</option>
                    </select>
                    <div class="field-title field-title--cost">Стоимость</div>
                        <input type="text" class="field-cost text" value name="cost" v-model="productFields.cost">
                    <div class="field-title">Параметры отправляемого груза</div>
                    <div class="field-group">
                        <input type="text" value="" id="p-width" class="text" placeholder="Ширина, см" name="width"
                               v-model="productFields.width">
                        <input type="text" value="" id="p-height" class="text" placeholder="Высота, см" name="height"
                               v-model="productFields.height">
                    </div>
                    <div class="field-group">
                        <input type="text" value="" id="p-depth" class="text" placeholder="Глубина, см" name="depth"
                               v-model="productFields.depth">
                        <input type="text" value="" id="p-amount" class="text" placeholder="Количество" name="count"
                               v-model="productFields.count">
                    </div>
                    <div class="field-group">
                        <input type="text" value="" id="p-volume" class="text" placeholder="Объем, м2" name="volume"
                               v-model="productFields.volume">
                        <input type="text" value="" id="est-weight" class="text" placeholder="Расчетный вес, кг" name="weight"
                               v-model="productFields.weight">
                    </div>
                    <div class="field-title">Признак склада отгрузки</div>
                    <select class="select" name="warehouse_address" v-model="applicationFields.warehouse_address">
                        <option>Выберите адрес</option>
                        <option>ул. Ленина</option>
                        <option>ул. Пушкина</option>
                    </select>
<!--                    <input type="text" value="" id="delivery-date" class="text" placeholder="Дата доставки" name="delivery_date"-->
<!--                           v-model="fields.delivery_date">-->
                    <date-picker input-class="text" v-model="applicationFields.delivery_date"
                                 valueType="YYYY-MM-DD"
                                 class="delivery-date"
                                 format="DD.MM.YYYY"
                                 placeholder="Дата доставки"
                        >
                    </date-picker>

                    <div class="field-title">Время доставки</div>
                    <div class="field-group field-group--time">
                        <div class="field-group__label">с</div>
<!--                        <select class="select" name="delivery_from" v-model="fields.delivery_from">-->
<!--                            <option>9:00</option>-->
<!--                            <option>10:00</option>-->
<!--                            <option>11:00</option>-->
<!--                        </select>-->
                        <date-picker format="H:mm"
                                     class="date-time"
                                     input-class="text time-picker"
                                     type="time"
                                     v-model="applicationFields.delivery_from"
                                     name="delivery_from"
                                     value-type="H:mm"
                                     :timePickerOptions="{
                                        start: '09:00',
                                        step: '01:00',
                                        end: '19:00',
                                     }">
                        </date-picker>
                        <div class="field-group__label">до</div>
<!--                        <select class="select" name="delivery_till" v-model="fields.delivery_till">-->
<!--                            <option>18:00</option>-->
<!--                            <option>19:00</option>-->
<!--                            <option>20:00</option>-->
<!--                        </select>-->
                        <date-picker format="H:mm"
                                     class="date-time"
                                     input-class="text time-picker"
                                     type="time"
                                     v-model="applicationFields.delivery_till"
                                     name="delivery_till"
                                     value-type="H:mm"
                                     :timePickerOptions="{
                                        start: '09:00',
                                        step: '01:00',
                                        end: '19:00',
                                     }">
                        </date-picker>
                    </div>
                    <div class="field-title">Адрес доставки</div>
                    <div>
                        <input type="text" value="" id="address" class="text"
                               placeholder="Московская обл, г Луховицы, деревня Асошники, ул Самара, д 1"
                               name="delivery_address" @keyup="getAddress($event)" v-model="addressFields.delivery_address">
                        <div v-if="suggestions.length > 0" class="suggestions-block">
                            <ul class="suggestions">
                                <li v-for="(item, index) in suggestions" @click="setAddress(index)">{{ item.value }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="field-group">
                        <input type="text" value="" id="flat" class="text" placeholder="Квартира" name="flat"
                               v-model="addressFields.flat">
                        <input type="text" value="" id="floor" class="text" placeholder="Этаж" name="floor"
                               v-model="addressFields.floor">
                    </div>
                    <div class="field-group">
                        <input type="text" value="" id="entrance" class="text" placeholder="Подъезд" name="entrance"
                               v-model="addressFields.entrance">
                        <input type="text" value="" id="postcode" class="text" placeholder="Почтовый индекс" name="postcode"
                               v-model="addressFields.postcode">
                    </div>
                    <div class="field-checkbox">
                        <input id="remember" type="checkbox" class="field-checkbox__input" name="elevator" v-model="addressFields.elevator">
                        <label class="field-checkbox__label" for="remember">Возможно ли использование лифта для доставки?</label>
                    </div>
                    <textarea class="field-textarea" v-model="applicationFields.comment" name="comment"></textarea>
                    <div class="field-title">Информация о покупателе</div>
                    <input type="text" value="" id="user" class="text" placeholder="ФИО покупателя" name="client_name"
                           v-model="applicationFields.client_name">
                    <input type="text" value="" id="phone" class="text" placeholder="Телефон покупателя" name="client_phone"
                           v-model="applicationFields.client_phone">
                </form>
            </div>
        </div>
        <div class="request-content">
            <div class="btn request-btn js-open-modal" data-modal="modal-request1" @click="submit">
                <svg width="20" height="18" viewBox="0 0 20 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.99991 9.48832L13.5357 13.0233L12.3566 14.2025L10.8332 12.6792V17.3333H9.16658V12.6775L7.64325 14.2025L6.46408 13.0233L9.99991 9.48832ZM9.99991 0.666657C11.4307 0.666725 12.8116 1.19258 13.8801 2.14426C14.9485 3.09593 15.63 4.40704 15.7949 5.82832C16.8318 6.11109 17.7363 6.74924 18.3505 7.63126C18.9646 8.51328 19.2492 9.58307 19.1546 10.6536C19.06 11.7242 18.5922 12.7275 17.8329 13.4882C17.0736 14.2488 16.0712 14.7185 15.0007 14.815V13.1367C15.3842 13.0819 15.7529 12.9513 16.0854 12.7525C16.4179 12.5536 16.7074 12.2906 16.937 11.9787C17.1667 11.6667 17.332 11.3122 17.4231 10.9357C17.5143 10.5592 17.5296 10.1683 17.468 9.78588C17.4065 9.40343 17.2694 9.03708 17.0647 8.7082C16.86 8.37932 16.5919 8.0945 16.2759 7.87038C15.96 7.64626 15.6025 7.48732 15.2245 7.40283C14.8465 7.31835 14.4554 7.31002 14.0741 7.37832C14.2046 6.77073 14.1975 6.14163 14.0534 5.53712C13.9093 4.9326 13.6318 4.36798 13.2412 3.8846C12.8507 3.40122 12.3569 3.01133 11.7961 2.74349C11.2354 2.47564 10.6218 2.33663 10.0003 2.33663C9.37887 2.33663 8.76529 2.47564 8.20452 2.74349C7.64375 3.01133 7.14999 3.40122 6.75941 3.8846C6.36884 4.36798 6.09134 4.9326 5.94723 5.53712C5.80313 6.14163 5.79607 6.77073 5.92658 7.37832C5.16629 7.23555 4.38043 7.40064 3.74187 7.83729C3.1033 8.27394 2.66435 8.94637 2.52158 9.70666C2.3788 10.4669 2.5439 11.2528 2.98055 11.8914C3.41719 12.5299 4.08963 12.9689 4.84991 13.1117L4.99991 13.1367V14.815C3.92945 14.7186 2.92692 14.2491 2.16752 13.4885C1.40813 12.7279 0.940184 11.7246 0.845474 10.654C0.750763 9.5834 1.03531 8.51356 1.64939 7.63147C2.26346 6.74938 3.168 6.11115 4.20491 5.82832C4.36967 4.40697 5.05108 3.09575 6.11956 2.14404C7.18804 1.19234 8.56904 0.666543 9.99991 0.666657Z" fill="white"/>
                </svg>
                <span>Создать заявку</span>
            </div>
            <div class="request-info">
                Информация / пояснение о загрузке данных от куда либо. <br>
                Возможно инструкция к загрузке?
            </div>
        </div>
        <popup :show="showModal" @handlerClose="show"></popup>
    </section>
</template>

<script>
import Popup from "./Popup";
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css';
import 'vue2-datepicker/locale/ru';

export default {
    name: "ApplicationForm",
    data() {
        return {
            applicationFields: {},
            productFields: {},
            addressFields: {
                elevator: false
            },
            showModal: false,
            address: null,
            hours: Array.from({ length: 10 }).map((_, i) => i + 8),
            suggestions: [],
            selectedAddress: null,
        }
    },
    methods: {
        submit() {
            axios.post('application-create', {
                fields: this.applicationFields,
                address:this.selectedAddress.data,
                addressExtraInfo: this.addressFields,
                products: this.productFields
            }).then((response) => {
                this.showModal = true
            }).catch((response) => {
                console.log(response)
            })

        },
        show(show) {
            this.showModal = show
            // очищаем поля
            Object.assign(this.$data, this.$options.data())
        },
        setAddress(index) {
            this.selectedAddress = this.suggestions[index]
            this.addressFields.delivery_address = this.selectedAddress.value
            this.suggestions = []
        },
        getAddress(event) {
            axios.post('get-address', {input: event.target.value}).then((response) => {
                this.suggestions = response.data
            })
        }
    },
    components: {
        Popup,
        DatePicker
    }
}
</script>

<style scoped>
.delivery-date {
    width: 100%;
}
.date-time {
        width: 310px !important;
        max-width: 310px !important;
}
.field-group {
    justify-content: unset;
}
.suggestions {
    list-style: none;
    border: 1px solid #ddd;
    position: absolute;
    z-index: 10;
    background-color: white;
    width: 100%;
    top:-44px;
    padding-left: 10px;
}

.suggestions li:hover {
    cursor: pointer;
    background-color: #ddd;
}

.suggestions-block {
    position: relative;
}
</style>
