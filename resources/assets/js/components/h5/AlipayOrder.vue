<template>
    <div style="margin-top: 20px">
        <div class="body" style="margin: 0;padding: 0;height: 100%">
            <div class="main" style="width: 100%;margin: 0;padding: 0;">
                <div class="head" style="position:fixed;width: 100%;top: 0;line-height:150px;z-index: 9999;">
                    <van-swipe :autoplay="3000" :height="150" indicator-color="white" class="imageStyle">
                        <van-swipe-item v-if="store.banner1"><img :src="store.banner1"></van-swipe-item>
                        <van-swipe-item v-if="store.banner2"><img :src="store.banner2"></van-swipe-item>
                        <van-swipe-item v-if="store.banner3"><img :src="store.banner3"></van-swipe-item>
                    </van-swipe>
                </div>
                <div style="position:absolute;width: 100%;margin-top: 150px;">
                    <div style="width: 20%;position:fixed;">
                        <van-sidebar v-model="activeKey">
                            <van-sidebar-item v-for="category,key in categories" :title="category.name"
                                              @click="check(category.id, key)"/>
                        </van-sidebar>
                    </div>
                    <div style="width: 80%;position:absolute;margin-left: 75px;overflow:scroll" id="dish">
                        <van-swipe-cell :right-width="10" :on-close="onClose" v-for="category in dishData"
                                        :key="category.id" :id="category.id">
                            <div v-for="dish in category.dishes">
                                <van-cell-group>
                                    <van-card
                                            :price="dish.amount"
                                            :title="dish.name"
                                            :thumb="dish.logo"
                                            :origin-price="dish.original_amount"
                                    >
                                        <div slot="footer">
                                            <div class="amount_container">
                                                <div class="amount_box">
                                                    <van-button size="mini" @click="sub(dish)">-</van-button>
                                                    <input type="number" style="width: 40px; text-align: center;"
                                                           value=0 :id="'number'+dish.id" maxlength="2" pattern="[0-9]*"
                                                           readonly="readonly"/>
                                                    <van-button size="mini" @click="add(dish)">+</van-button>
                                                </div>
                                            </div>
                                        </div>
                                    </van-card>
                                </van-cell-group>
                            </div>
                        </van-swipe-cell>
                    </div>
                </div>
                <van-submit-bar
                        id="pay"
                        :price="totalAmount"
                        button-text="提交订单"
                        @submit="onSubmit()"
                />
            </div>
        </div>
    </div>
</template>

<script>
    import {Toast} from 'vant';
    import Vue from 'vue';
    import {
        GoodsAction,
        // GoodsActionBigBtn,
        // GoodsActionMiniBtn
    } from 'vant';

    Vue
        .use(GoodsAction)
    // .use(GoodsActionBigBtn)
    // .use(GoodsActionMiniBtn);

    export default {
        data() {
            return {
                activeKey: 0,
                categories: {},
                store: {},
                form: {
                    account: '',
                    fee: '',
                },
                accountOptions: [],
                accountShow: false,
                showNumber: false,
                images: [
                    "h5.keeper.test/images/banner1.jpg",
                    "h5.keeper.test/images/banner2.jpg",
                ],
                imageURL: "",
                hot: '热卖中',
                price: '',
                originPrice: '',
                desc: '微辣',
                title: '',
                number: 0,
                totalAmount: 0,
                dishData: {},
                customerDishDetail: []
            };
        },
        created() {
        },
        computed: {},
        mounted() {
            this.handleCategories();
            this.handleBanner();
            this.dishes();
        },
        methods: {
            check(category_id, key) {
                if (key === 0) {
                    document.documentElement.scrollTop = 1;
                } else {
                    document.getElementById(category_id).scrollIntoView();
                }
            },
            dishes(category_id) {
                let merchant_id = this.$route.query.merchant_id;
                // console.log(merchant_id);
                this.$api.h5DishList({merchant_id: merchant_id, category_id: category_id}).then(res => {
                    if (res.status === 1) {
                        this.dishData = res.data;
                    } else if (res.status === 3) {
                        Toast.fail(res.message);
                        // this.$router.push({name: 'login', query: {}});
                    } else {
                        Toast.fail(res.message);
                    }
                });
            },
            handleCategories() {
                let merchant_id = this.$route.query.merchant_id;
                this.$api.h5Category({merchant_id: merchant_id}).then(res => {
                    this.categories = res.data;
                    // let category_id = res.data[0].id;
                    // this.dishes(category_id);
                }).catch(err => {
                    this.$message({
                        type: 'error',
                        message: '类目获取失败'
                    });
                });
            },
            handleBanner() {
                let merchant_id = this.$route.query.merchant_id;
                this.$api.h5Banner({merchant_id: merchant_id}).then(res => {
                    this.store = res.data;
                }).catch(err => {
                    this.$message({
                        type: 'error',
                        message: 'store获取失败'
                    });
                });
            },
            sub(dish) {
                let dishId = dish.id;
                let id = 'number' + dishId;
                let numberObject = document.getElementById(id);
                let numberValue = Number(numberObject.value);
                let finalNumber = 0;

                if (numberValue > 0) {
                    numberObject.setAttribute("style", "width: 40px; text-align: center;color: #f44");
                    finalNumber = numberValue - 1;
                    numberObject.value = finalNumber;

                    if (this.customerDishDetail.length > 0) {
                        for (let i = 0; i < this.customerDishDetail.length; i++) {
                            if (this.customerDishDetail[i].dish_id === dishId) {
                                this.customerDishDetail[i].number = finalNumber;

                                if (finalNumber < 1) {
                                    this.customerDishDetail.splice(i, 1);
                                }

                                // 总价减去次价格
                                this.totalAmount -= Number(dish.amount) * 100;
                            }
                        }
                    }
                }
                if (finalNumber < 1) {
                    numberObject.setAttribute("style", "width: 40px; text-align: center;");
                }

                // if (this.totalAmount > 0) {
                //     document.getElementById('pay').attributes("style", "color: #fff;background-color: #f44;border: 1px solid #f44;")
                // } else {
                //     document.getElementById('pay').attributes("style", "color: #fff;background-color: #fff;border: 1px solid #fff;")
                // }
            },
            add(dish) {
                let dishId = dish.id;
                let id = 'number' + dishId;
                let numberObject = document.getElementById(id);
                let numberValue = Number(numberObject.value);
                let finalNumber = 0;
                numberObject.setAttribute("style", "width: 40px; text-align: center;color: #f44");

                finalNumber = numberValue + 1;
                numberObject.value = finalNumber;
                // console.log(numberObject.value);
                if (this.customerDishDetail.length > 0 && numberValue > 0) {
                    for (let i = 0; i < this.customerDishDetail.length; i++) {
                        if (this.customerDishDetail[i].dish_id === dishId) {
                            this.customerDishDetail[i].number = finalNumber;

                            // 总价增加
                            this.totalAmount += Number(dish.amount) * 100;
                        }
                    }
                } else {
                    let newJson = {};
                    newJson.dish_id = dishId;
                    newJson.number = finalNumber;
                    this.customerDishDetail.push(newJson);

                    // 总价增加
                    this.totalAmount += Number(dish.amount) * 100;
                }

                // if (this.totalAmount > 0) {
                //     document.getElementById('pay').attributes("style", "color: #fff;background-color: #f44;border: 1px solid #f44;")
                // } else {
                //     document.getElementById('pay').attributes("style", "color: #fff;background-color: #fff;border: 1px solid #fff;")
                // }

                // console.log(this.customerDishDetail);
                // console.log(Number(dish.amount), this.totalAmount, dish.amount);
            },
            onSubmit() {
                // console.log(this.totalAmount);
                if (this.totalAmount > 0) {
                    let amount = this.totalAmount;
                    let detail = this.customerDishDetail;
                    let open_id = this.$route.query.open_id;
                    let merchant_id = this.$route.query.merchant_id;
                    let seat_id = this.$route.query.seat_id;
                    let table_id = this.$route.query.table_id;
                    let query = this.$route.query;
                    let jsApiParameters = '';
                    this.$api.h5Pay({
                        amount: amount,
                        detail: detail,
                        open_id: open_id,
                        merchant_id: merchant_id,
                        seat_id: seat_id,
                        table_id: table_id,
                        query: query
                    }).then(res => {
                        // this.$message({
                        //     type: 'info',
                        //     message: document.getElementsByTagName('body')[0].append(res.pay_form)
                        // });
                        // alert(data.pay_form);
                        if (res.status === 1) {
                            // this.html = res.pay_form;
                            let form = res.pay_form;
                            const div = document.createElement('div');
                            div.innerHTML = form;
                            document.body.appendChild(div);
                            document.forms[0].submit();
                        } else {
                            this.$message({
                                type: 'info',
                                message: '网络错误，请稍后再试'
                            });
                        }
                    }).catch(error => {
                        this.$message({
                            type: 'info',
                            message: error
                        });
                    });
                }
                // console.log(this.$route.params);
            },
            onConfirmAccount(value, index) {
                this.form.account = value;
                this.accountShow = false;
            },
            onInput(key) {
                this.form.fee += key;
            },
            onDelete() {
                this.form.fee = '';
            },
            onClose() {
                this.showNumber = false;
            },
            // 表单提交
            onSubmitForm() {
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.$api.FinanceWithdrawApply(this.form).then(res => {
                            if (res.status === 1) {
                                Toast.success(res.message);
                            } else if (res.status === 3) {
                                Toast.fail(res.message);
                                this.$router.push({name: 'login', query: {}});
                            } else {
                                Toast.fail(res.message);
                            }
                        }).catch(err => {
                            Toast.fail('表单数据异常!');
                        });
                    }
                });
            }
        }
    }
</script>

<style lange="less">
    .imageStyle img {
        width: 100%;
        height: 100%;
        display: block;
    }

    .van-card__thumb {
        width: 100px;
        height: 70px;
        margin-right: 10px;
        /* -webkit-box-align: center; */
        /* -webkit-align-items: center; */
        /* align-items: center; */
        /* -webkit-box-pack: center; */
        -webkit-justify-content: center;
        /* justify-content: center; */
        /* -webkit-box-flex: 0; */
        -webkit-flex: none;
        /* flex: none; */
        /* margin-top: 20px; */
    }

    .van-button--warning {
        color: #fff;
        background-color: #409eff;
        border: 1px solid #409eff;
    }

    .van-card {
        position: relative;
        color: #323233;
        padding: 10px 15px;
        font-size: 14px;
        box-sizing: border-box;
        background-color: #fafafa;
    }

    .van-card__content, .van-card__header {
        height: 44px;
    }

    .van-card__title {
        line-height: 16px;
        max-height: 32px;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        padding-top: 5px;
    }

    .van-card__bottom, .van-card__desc {
        line-height: 20px;
        margin-top: 4px;
        font-size: 14px;
    }

</style>
