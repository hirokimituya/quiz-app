<template>
  <app-layout title="パスワード再発行">
    <v-card max-width="550" class="mx-auto px-sm-16 mt-5 mt-md-10 pb-6">
      <v-card-text class="">
        <v-alert
          v-model="sendSuccess"
          dismissible
          type="success"
          border="left"
          transition="slide-x-transition"
          dense
        >
          メールを送信しました。
        </v-alert>

        <div class="my-5">
          パスワードを忘れた場合は、以下に登録メールアドレスを入力して送信してください。パスワード再発行リンクを送信します。
        </div>

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

          <v-btn
            type="submit"
            block
            color="primary darken-2"
            class="white--text mb-5"
            :disabled="form.processing"
            @click.prevent="submit()"
          >
            パスワード再発行リンクを送信する
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
    status: {
      type: String,
      default: '',
    },
  },
  data() {
    return {
      form: this.$inertia.form({
        email: '',
      }),
      sendSuccess: false,
    }
  },
  methods: {
    submit() {
      this.form.post(this.route('password.email'), {
        onSuccess: () => (this.sendSuccess = true),
      })
    },
  },
}
</script>
