<template>
    <div class="container my-2">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Статусы заявок</h3>
                        <input type="text" class="form-control col-4 filter" v-model="filter" placeholder="Поиск...">
                    </div>
                    <div class="card-body" ref="card">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                <tr>
                                    <th>Номер заказа</th>
                                    <th>Партнёр</th>
                                    <th>Статус</th>
                                    <th>Дата создания</th>
                                    <th>Последнее обновление</th>
                                    <th>Обработан в 1с</th>
                                </tr>
                                </thead>
                                <tbody v-if="apps">
                                <tr v-for="(app,index) in filteredRows" :key="index">
                                    <td>{{ app.order_number }}</td>
                                    <td>{{ app.user.company }}</td>
                                    <td>{{ rusStatus(app.status) }}</td>
                                    <td>{{ timestampToDate(app.created_at) }}</td>
                                    <td>{{ timestampToDate(app.updated_at) }}</td>
                                    <td><input type="checkbox" :checked="isSendedTo1c(app)" disabled></td>
                                </tr>
                                </tbody>
                                <tbody v-else>
                                <tr>
                                    <td align="center" colspan="3">No record found.</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
<!--                        <pagination align="center" :data="apps" :limit="10" @pagination-change-page="list"></pagination>-->
                        <p>
                            <button type="button" class="btn btn-secondary" @click="prevPage">Предыдущая</button>
                            <button type="button" class="btn btn-secondary" @click="nextPage">Следующая</button>
                            <span style="padding-left: 5px">{{ currentPage }} из {{ totalPage }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import pagination from 'laravel-vue-pagination'

export default {
    name: "SupportApps",
    components: {
        pagination
    },
    data() {
        return {
            apps: [],
            statuses: {
                'created': 'Создано',
                'new': 'Новый',
                'inProgress': 'В работе',
                'loaded': 'Загружен',
                'postponed': 'Отложен',
                'refusal': 'Отказ',
                'completed': 'Выполнен',
                'defect': 'Брак'
            },
            isSended: false,
            filter: '',
            pageSize: 20,
            currentPage: 1,
        }
    },
    mounted() {
        this.list()
    },
    methods: {
        list() {
            axios.get('/admin/support/apps/get')
                .then(response => {
                    this.apps = response.data;
                });
        },
        rusStatus(status) {
            return this.statuses[status]
        },
        timestampToDate(timestamp) {
            let date = new Date(timestamp)
            let hours = date.getHours();

            // Minutes part from the timestamp
            let minutes = "0" + date.getMinutes();

            // Seconds part from the timestamp
            let seconds = "0" + date.getSeconds();
            let month = date.getMonth() + 1
            let day = date.getDate();

            if (month < 10) {
                month = `0${month}`;
            }

            if (day < 10) {
                day = `0${day}`;
            }

            // Will display time in 10:30:23 format
            return day + '-' + month + '-' + date.getFullYear() + ' ' +
                hours + ':' + minutes.slice(-2) + ':' + seconds.slice(-2)
        },
        isSendedTo1c(app) {
            return app.doc_ver === app.old_doc_ver && app.status != 'created';
        },
        nextPage() {
            if ((this.currentPage * this.pageSize) < this.apps.length) this.currentPage++;
        },
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },
    },
    computed: {
        filteredRows() {
            return this.apps.filter((app, index) => {
                const order = app.order_number.toLowerCase();
                const searchTerm = this.filter.toLowerCase();
                let start = (this.currentPage - 1) * this.pageSize;
                let end = this.currentPage * this.pageSize;
                if (this.filter !== '') return order.includes(searchTerm)
                if (index >= start && index < end)
                    return true
            });
        },
        totalPage() {
            return Math.ceil(this.apps.length / this.pageSize)
        }
    }
}
</script>

<style scoped>
    input[type=checkbox] {
        transform: scale(2);
    }
</style>