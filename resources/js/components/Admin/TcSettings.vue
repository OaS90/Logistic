<template>
    <table class="settings">
        <thead>
            <tr>
                <th>Наименование</th>
                <th>Вкл./Выкл</th>
                <th style="width: 150px">Ограничение по времени</th>
                <th style="width: 300px">Дни отгрузки</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(setting, id) in settings" :key="`warehouse-${id}`">
                <td>{{ setting.tc_name }}</td>
                <td align="center"><input type="checkbox" :checked="setting.enabled" @change="toggleEnable(setting)"></td>
                <td>
                    <date-picker type="time"
                                 class="last-time"
                                 v-model:value="setting.last_time"
                                 format="H:mm"
                                 value-type="H:mm"
                                 :timePickerOptions="{
                                            start: '01:00',
                                            step: '01:00',
                                            end: '23:00',
                                         }">
                    </date-picker>
                </td>
                <td>
                    <div class="days">
                        <label for="Mon">Пн</label>
                        <input type="checkbox" :checked="setting.days[1]" name="Mon" @change="addDay(setting, 1)">
                    </div>
                    <div class="days">
                        <label for="Tues">Вт</label>
                        <input type="checkbox" :checked="setting.days[2]" name="Tues" @change="addDay(setting, 2)">
                    </div>
                    <div class="days">
                        <label for="Wed">Ср</label>
                        <input type="checkbox" :checked="setting.days[3]" name="Wed" @change="addDay(setting, 3)">
                    </div>
                    <div class="days">
                        <label for="Thurs">Чт</label>
                        <input type="checkbox" :checked="setting.days[4]" name="Thurs" @change="addDay(setting, 4)">
                    </div>
                    <div class="days">
                        <label for="Fri">Пт</label>
                        <input type="checkbox" :checked="setting.days[5]" name="Fri" @change="addDay(setting, 5)">
                    </div>
                    <div class="days">
                        <label for="Sat">Сб</label>
                        <input type="checkbox" :checked="setting.days[6]" name="Sat" @change="addDay(setting, 6)">
                    </div>
                    <div class="days">
                        <label for="Sun">Вс</label>
                        <input type="checkbox" :checked="setting.days[7]" name="Sun" @change="addDay(setting, 7)">
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
import DatePicker from "vue-datepicker-next";

export default {
    name: "TcSettings",
    props: ['settings'],
    components: {
        DatePicker
    },
    data() {
        return {
            show: false
        }
    },
    methods: {
        addDay(setting, day) {
            setting.days[day] = !setting.days[day]
        },
        toggleEnable(setting) {
            setting.enabled = !setting.enabled
        }
    }
}
</script>

<style scoped>
input, label {
    display: block;
}
.days {
    float: left;
    padding-right: 15px;
}
input[type=checkbox] {
    transform: scale(1.5);
}
.last-time {
    width: 90px;
}
</style>
