<template>
    <div class="w-full rounded shadow">
        <template v-if="agenda.agenda">
            <table>
                <template v-for="(day,day_ind) in agenda.agenda">

                    <tr>
                        <th>{{day.date.split('-').reverse().join('-')}}</th>
                        <td>
                            <time-bar :key="day_ind"
                                          :min="agenda.start"
                                          :max="agenda.eind"
                                          :length="700"
                            ></time-bar>
                        </td>
                    </tr>
                    <tr v-if="day.appointments">
                        <td>Afspraken</td>
                        <td>
                            <appointments-bar :key="day_ind+'a'"
                                                          :min="agenda.start"
                                                          :max="agenda.eind"
                                                          :length="700"
                                                          :appointments="day.appointments"
                            ></appointments-bar>
                        </td>
                    </tr>
                    <tr v-for="(activity,act_ind) in day.activities">
                        <td>{{activity.name}}</td>
                        <td>
                            <activity-bar :key="day_ind+'p'"
                                                      :min="agenda.start"
                                                      :max="agenda.eind"
                                                      :length="700"
                                                      :activity="activity"
                            ></activity-bar>
                        </td>
                    </tr>


                </template>
            </table>
            <div v-if="agenda.agenda.length===0" class="notification is-danger">Geen planning voor deze periode</div>
        </template>
    </div>
</template>

<script>
    export default {
        name: "Preview",
        props: {
            agenda: {
                type: Object,
                required: true,
            },
        },
        data() {
            return {}
        },
        /*
        watch: {
            rule: {
                deep: true,
                immediate: true,
                handler(){
                }
            },
        },
        */
    }
</script>

<style scoped>

</style>
