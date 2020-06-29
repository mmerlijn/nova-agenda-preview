<template>
    <div class="timebarStart">
        <div :style="{ width: length +'px'}" class="achtergrond">
            <template v-for="(obj,ind) in activity.times">
                <div
                    :key="ind"
                    :style="{marginLeft: position(obj[0]) +'px', width: width(obj[0],obj[1])+'px',backgroundColor: activity.color,zIndex: ind}"
                    :title="title(obj)"
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
            activity:{
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
                return this.factor * (n - this.start);
            },
            width(van, tot) {
                return this.factor * (tot - van);
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

