<template>

    <div class="timebarStart">
        <div :style="{ width: length +'px'}" class="tweede">
            <div :style="{marginLeft: 0 +'px', width: halfuur+'px'}" class="eerste">&nbsp;&nbsp</div>
            <template v-for="n in aantal_uren">
                <div :style="{marginLeft: (n-1)*uur_grootte +'px', width: halfuur+'px'}" class="eerste">&nbsp;&nbsp
                </div>
                <div class="half" :style="{marginLeft: bereken_half(n-1)+'px'}" v-show="toon_half(n-1)">{{tijd_half[(n-1)+startuur]}}</div>
                <div class="heel" :style="{marginLeft: bereken(n)+'px'}" v-show="toon(n)">{{tijd[n+startuur]}}</div>
            </template>
        </div>
    </div>
</template>

<script>
    export default {

        //props: ['min','max','lengte']
        props: {
            min: {
                type: Number,
                default: 480
            }
            , max: {
                type: Number,
                default: 1020
            }
            , length: {
                type: Number,
                default: 600
            }
        }
        , data() {
            return {
                tijd: [" 0:00", " 1:00", " 2:00", " 3:00", " 4:00", " 5:00", " 6:00", " 7:00", " 8:00", " 9:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00", "24:00"],
                tijd_half: [" 0:30", " 1:30", " 2:30", " 3:30", " 4:30", " 5:30", " 6:30", " 7:30", " 8:30", " 9:30", "10:30", "11:30", "12:30", "13:30", "14:30", "15:30", "16:30", "17:30", "18:30", "19:30", "20:30", "21:30", "22:30", "23:30"],
                startuur: 7
                ,
                einduur: 17
                ,
                aantal_uren: 10
                ,
                uur_grootte: 50
                ,
                min_grootte: 0.833
                ,
                starttijd: 420
                ,
                eindtijd: 120
                ,
                halfuur: 25
                ,
                bgcolor: '#B6B6B6'
                ,
                bgcolorBlanc: '#DDDDDD'
            }
        },
        watch: {
            min: {
                handler: function (val, oldVal) {
                    this.factorBar();
                },
                deep:true
            },
            max: {
                handler: function (val, oldVal) {
                    this.factorBar();
                },
                deep:true
            }
        },
        methods: {
            bereken(n) {
                if (this.uur_grootte >= 22) {
                    if (n - 1 + this.startuur < 9) {
                        var extra = -12;
                    }else {
                        var extra = -20;
                    }
                    return extra + n * this.uur_grootte;

                } else {
                    return -20 + n * this.uur_grootte
                }
                return 0;
            },
            bereken_half(n) {
                if (this.uur_grootte >= 22) {
                    if (n - 1 + this.startuur < 9) {
                        var extra = -12;
                    } else {
                        var extra = -20;
                    }
                    return extra + n * this.uur_grootte + this.uur_grootte/2;

                } else {
                    return -20 + n * this.uur_grootte + this.uur_grootte/2
                }
                return 0;
            },
            toon(n) {
                if (n < this.aantal_uren) {
                    if (this.uur_grootte > 47) {
                        return true;
                    } else if (this.uur_grootte >= 22) {
                        if (n % 2 == 1) {
                            return true;
                        }
                    } else {
                        if (n % 3 == 1) {
                            return true;
                        }
                    }
                }
                return false;
            },
        toon_half(n) {
            if (n < this.aantal_uren) {
                if (this.uur_grootte > 100) {
                    return true;
                }
            }
            return false;
        },
            factorBar: function(){
                this.startuur = Math.floor(this.min / 60);
                this.einduur = Math.ceil(this.max / 60);
                this.aantal_uren = this.einduur - this.startuur;
                this.uur_grootte = this.length / this.aantal_uren;
                this.min_grootte = this.uur_grootte / 60;
                this.starttijd = this.startuur * 60;
                this.eindtijd = this.einduur * 60;
                this.halfuur = this.uur_grootte / 2;
            }
        }
        , mounted() {
            this.factorBar();
        }
    }
</script>

<style scoped>

    div{
        position: absolute;
        height: 20px;
        padding-left: 0;
        z-index:2;
    }

    div.tweede {
        background-color: #DDDDDD;
        z-index:0;
    }

    div.eerste {
        background-color: #B6B6B6;
        z-index:1;
    }
    div.timebarStart {
        position: relative;
    }
    .half{
        position: absolute;
        margin-top:-10px;
        font-weight: bold;
    }
    .heel{
        position: absolute;
        margin-top:2px;
        font-weight: bold;
    }
</style>
