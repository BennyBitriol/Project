<template>
    <div>
        <h1>Login with Line, Please wait!</h1>
    </div>
</template>
<script>
import {mapActions} from 'vuex'
export default {
    methods: {
        ...mapActions({
            loginLine: 'auth/loginLineCallback'
        }),
        loginUser(){
            this.loginLine({
                code: this.$route.query.code
            })
            .then(this.$router.push('/'))
        }
    },
    created() {
        this.loginUser()
    },
    
};
</script>