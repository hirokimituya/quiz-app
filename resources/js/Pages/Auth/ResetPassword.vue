<template>
  <app-layout>
    <v-card max-width="550" class="mx-auto px-sm-16 mt-5 mt-md-10 pb-6">
      <v-card-title class="primary--text text-h5 d-block text-center">
        パスワードリセット
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
            type="password"
            required
            autocomplete="new-password"
          ></v-text-field>

          <v-text-field
            v-model="form.password_confirmation"
            label="パスワード（確認用）"
            type="password"
            required
            autocomplete="new-password"
          ></v-text-field>

          <v-btn
            type="submit"
            block
            color="primary darken-2"
            class="white--text mb-5 mt-5"
            :disabled="form.processing"
            @click.prevent="submit()"
          >
            パスワードをリセット
          </v-btn>
        </v-form>
      </v-card-text>
    </v-card>
  </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout'
import AlertValidation from '@/Components/AlertValidation'

export default {
  components: {
    AppLayout,
    AlertValidation,
  },
  props: {
    email: {
      type: String,
      default: '',
    },
    token: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      form: this.$inertia.form({
        token: this.token,
        email: this.email,
        password: '',
        password_confirmation: '',
      }),
    }
  },
  methods: {
    submit() {
      this.form.post(this.route('password.update'), {
        onFinish: () => this.form.reset('password', 'password_confirmation'),
      })
    },
  },
}
</script>
