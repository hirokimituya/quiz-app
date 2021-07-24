<template>
  <app-layout>
    <v-card max-width="550" class="mx-auto px-sm-16 mt-5 mt-md-10 pb-6">
      <v-card-title class="primary--text text-h5 d-block text-center">
        クイズメーカーにログイン
      </v-card-title>

      <v-card-text>
        <!-- バリデーションエラー -->
        <alert-validation></alert-validation>

        <v-form>
          <v-text-field
            id="email"
            v-model="form.email"
            label="メールアドレス"
            type="email"
            required
            autofocus
          ></v-text-field>

          <v-text-field
            v-model="form.password"
            label="パスワード"
            :append-icon="showPassword ? mdiEye : mdiEyeOff"
            :type="showPassword ? 'text' : 'password'"
            required
            autocomplete="current-password"
            @click:append="showPassword = !showPassword"
          ></v-text-field>

          <v-checkbox
            v-model="form.remember"
            class="mt-n2"
            label="次回から自動ログイン"
          ></v-checkbox>

          <v-btn
            type="submit"
            block
            color="primary darken-2"
            class="white--text mb-5"
            :disabled="form.processing"
            @click.prevent="submit()"
          >
            ログイン
          </v-btn>

          <v-btn
            block
            outlined
            color="primary darken-2"
            @click.prevent="pwForget()"
          >
            パスワードを忘れた場合
          </v-btn>
        </v-form>
      </v-card-text>

      <v-divider class="mt-10"></v-divider>

      <!-- テストユーザログインボタン -->
      <div class="text-center">
        <v-btn
          color="green"
          class="white--text my-10"
          @click.prevent="testLogin"
        >
          テストユーザでログインします
        </v-btn>
      </div>
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import AlertValidation from '@/Components/AlertValidation'

import { mdiEye, mdiEyeOff } from '@mdi/js'

export default {
  components: {
    AppLayout,
    AlertValidation,
  },
  props: {
    canResetPass: Boolean,
    status: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      mdiEye,
      mdiEyeOff,
      form: this.$inertia.form({
        email: '',
        password: '',
        remember: false,
      }),
      showPassword: false,
    }
  },
  methods: {
    submit() {
      this.form
        .transform(data => ({
          ...data,
          remember: this.form.remember ? 'on' : '',
        }))
        .post(this.route('login'), {
          onFinish: () => this.form.reset('password'),
        })
    },
    pwForget() {
      this.$inertia.get(route('password.request'))
    },
    testLogin() {
      let test_user_login_data = {
        email: 'test@test.co.jp',
        password: 'testtest',
        remember: '',
      }

      this.$inertia.post(this.route('login'), test_user_login_data, {
        onFinish: () => this.form.reset('password'),
      })
    },
  },
}
</script>
