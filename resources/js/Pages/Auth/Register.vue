<template>
  <app-layout>
    <v-card max-width="550" class="mx-auto px-sm-16 mt-5 mt-md-10 pb-6">
      <v-card-title class="primary--text text-h5 d-block text-center">
        新規ユーザ登録
      </v-card-title>

      <v-card-text>
        <!-- バリデーションエラー -->
        <alert-validation></alert-validation>

        <v-form>
          <v-text-field
            id="name"
            v-model="form.name"
            label="ユーザ名"
            type="text"
            required
            autofocus
            autocomplete="name"
          ></v-text-field>

          <v-text-field
            id="email"
            v-model="form.email"
            label="メールアドレス"
            type="email"
            required
          ></v-text-field>

          <v-text-field
            v-model="form.password"
            label="パスワード"
            :append-icon="showPassword1 ? mdiEye : mdiEyeOff"
            :type="showPassword1 ? 'text' : 'password'"
            required
            autocomplete="new-password"
            @click:append="showPassword1 = !showPassword1"
          ></v-text-field>

          <v-text-field
            v-model="form.password_confirmation"
            label="パスワード（確認用）"
            :append-icon="showPassword2 ? mdiEye : mdiEyeOff"
            :type="showPassword2 ? 'text' : 'password'"
            required
            autocomplete="new-password"
            @click:append="showPassword2 = !showPassword2"
          ></v-text-field>

          <v-btn
            type="submit"
            block
            color="primary darken-2"
            class="white--text mb-5 mt-5"
            :disabled="form.processing"
            @click.prevent="submit()"
          >
            登録
          </v-btn>
        </v-form>
      </v-card-text>
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
  data() {
    return {
      mdiEye,
      mdiEyeOff,
      form: this.$inertia.form({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        terms: false,
      }),
      showPassword1: false,
      showPassword2: false,
    }
  },
  methods: {
    submit() {
      this.form.post(this.route('register'), {
        onFinish: () => this.form.reset('password', 'password_confirmation'),
      })
    },
  },
}
</script>
