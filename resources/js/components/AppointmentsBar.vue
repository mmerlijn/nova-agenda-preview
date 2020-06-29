<template>
    <div class="timebarStart">
        <div :style="{ width: length +'px'}" class="achtergrond">
            <template v-for="(appointment,ind) in appointments">
                <div
                    :key="ind"
                    :style="{marginLeft: position(appointment.van) +'px', width: width(appointment.van,appointment.tot)+'px',backgroundColor: appointment.activity.color,zIndex: ind}"
                    :title="appointment.name +' ('+appointment.dob+') ' + appointment.activity.name+' '+appointment.van+'-'+appointment.tot"
                    class="blok">
                </div>
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            min: {
                type: Number,
                default: 450
            }
            , max: {
                type: Number,
                default: 1020
            }
            , length: {
                type: Number,
                default: 800
            },
            appointments:{
                type:Object,
                required: true,
            }

        }
        , data() {
            return {
                factor: .926,
                start: 480,
                eind: 1020,
            }
        },
        watch: {
            min: {
                handler: function (val, oldVal) {
                    this.countFactor();
                },
                deep: true
            },
            max: {
                handler: function (val, oldVal) {
                    this.countFactor();
                },
                deep: true
            }
        },
        methods: {
            position(n) {
                return this.factor * (this.time2int(n) - this.start);
            },
            width(van, tot) {
                return this.factor * (this.time2int(tot) - this.time2int(van));
            },
            title(times) {
                return this.activity.name+' '+ this.int2time(times[0]) + '-' + this.int2time(times[1]) + ' ';
            },
            countFactor() {
                this.start = Math.floor(this.min / 60) * 60;
                this.eind = Math.ceil(this.max / 60) * 60;
                this.factor = this.length / (this.eind - this.start);
            },
            int2time(integer) {
                var uren = Math.floor(integer / 60);
                var min = integer - uren * 60;
                if (min < 10) {
                    min = '0' + min;
                }
                return uren + ':' + min;
            },
            time2int(time){
                return parseInt(time.split(":")[0],10)*60 + parseInt(time.split(":")[1],10);
            }

        }

        , mounted() {
            this.countFactor();

        }
    }
</script>

<style scoped>
    div {
        position: absolute;
        height: 20px;
        padding-left: 0;
        z-index: 2;
    }

    div.achtergrond {
        background-color: #f4f7fc;
        z-index: 0;
    }

    div.blok {
        background-color: forestgreen;
        z-index: 1;
        border-radius: 4px;
        line-height: 20px;
        text-align: center;
    }

    div.click-blok {
        cursor: pointer;
        border-left: 1px solid grey;
    }

    div.timebarStart {
        position: relative;
    }
</style>

