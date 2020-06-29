<template>
    <div>
        <div class="font-bold text-xl mb-2">Ruimte preview</div>
        <input class="form-control form-input form-input-bordered mb-3" title="vanaf" type="date" name="start_date" placeholder="vanaf" v-model="filter.start_date">

        <input class="form-control form-input form-input-bordered mb-3" type="number" title="aantal weken" name="weeks" v-model="filter.weeks">
        Met afspraken <input type="checkbox" value="1" v-model="filter.withAppointments">
        <button class="bg-primary m-3 p-2 rounded min-w-8" @click="preview">Preview</button>
        <preview :agenda="agenda"></preview>
    </div>
</template>

<script>
    export default {
        props: ['resourceName', 'resourceId', 'panel'],
        data() {
            return {
                filter: {
                    start_date: new Date().toJSON().slice(0,10),
                    weeks: 2,
                    withAppointments: false,
                },
                agenda:{}
            }
        },
        mounted() {
            //
        },
        methods: {
            preview() {
                this.axiosInterceptors();
                axios.post('/pa/agenda-preview/' + this.resourceId, {
                    filter: this.filter,
                }).then((r) => {
                    this.agenda = r.data;
                }).catch((e) => {
                    let bericht = '';
                    if (e.response.status === 422) {
                        bericht = "Niet alle vereiste parameters zijn meegestuurd, kan agenda niet laden.";
                    } else {
                        bericht = e.message;
                    }
                    this.$buefy.toast.open({
                        duration: 2500,
                        message: bericht,
                        type: 'is-danger',
                        position: 'is-top',
                        queue: false,
                    })
                });

            },
            axiosInterceptors() {
                let self = this;
                axios.interceptors.request.use((config) => {
                    // trigger 'loading=true' event here
                    self.loading = true;
                    return config;
                }, (error) => {
                    // trigger 'loading=false' event here
                    self.loading = false;
                    return Promise.reject(error);
                });

                axios.interceptors.response.use((response) => {
                    // trigger 'loading=false' event here
                    self.loading = false;
                    return response;
                }, (error) => {
                    // trigger 'loading=false' event here
                    self.loading = false;
                    return Promise.reject(error);
                });
            },
        }
    }
</script>
