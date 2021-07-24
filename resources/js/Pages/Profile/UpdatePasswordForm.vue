<template>
  <profile-form @submitted="updatePassword">
    <template #title>
      パスワード更新
    </template>

    <template #description>
      安全を確保するために、アカウントで長いランダムなパスワードを使用していることを確認してください。
    </template>

    <template #form>
      <!-- Current Password -->
      <div class="mb-2">
        <v-row no-gutters>
          <v-col cols="12" sm="9" md="7">
            <v-text-field
              id="current_password"
              ref="current_password"
              v-model="form.current_password"
              type="password"
              autocomplete="current-password"
              label="現在のパスワード"
              outlined
              dense
            ></v-text-field>
          </v-col>

          <v-col cols="12" sm="9" md="7" class="mt-n4">
            <!-- バリデーションエラー表示 -->
            <div v-if="form.errors.current_password">
              <v-alert
                color="error"
                border="left"
                dense
                class="white--text"
                elevation="2"
              >
                {{ form.errors.current_password }}
              </v-alert>
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- New Password -->
      <div class="mb-2">
        <v-row no-gutters>
          <v-col cols="12" sm="9" md="7">
            <v-text-field
              id="password"
              ref="password"
              v-model="form.password"
              type="password"
              autocomplete="new-password"
              label="新しいパスワード"
              outlined
              dense
            ></v-text-field>
          </v-col>

          <v-col cols="12" sm="9" md="7" class="mt-n4">
            <!-- バリデーションエラー表示 -->
            <div v-if="form.errors.password">
              <v-alert
                color="error"
                border="left"
                dense
                class="white--text"
                elevation="2"
              >
                {{ form.errors.password }}
              </v-alert>
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- Confirm Password -->
      <div class="mb-2">
        <v-row no-gutters>
          <v-col cols="12" sm="9" md="7">
            <v-text-field
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              autocomplete="new-password"
              label="新しいパスワード（確認用）"
              outlined
              dense
            ></v-text-field>
          </v-col>

          <v-col cols="12" sm="9" md="7" class="mt-n4">
            <!-- バリデーションエラー表示 -->
            <div v-if="form.errors.password_confirmation">
              <v-alert
                color="error"
                border="left"
                dense
                class="white--text"
                elevation="2"
              >
                {{ form.errors.password_confirmation }}
              </v-alert>
            </div>
          </v-col>
        </v-row>
      </div>
    </template>

    <template #actions>
      <span v-show="form.recentlySuccessful" class="text-subtitle-2 mr-2"
        >保存完了</span
      >

      <v-btn
        type="submit"
        color="black"
        class="white--text"
        :disabled="form.processing"
      >
        保存
      </v-btn>
    </template>
  </profile-form>
</template>

<script>
import ProfileForm from '@/Components/ProfileForm'

export default {
  components: {
    ProfileForm,
  },
  data() {
    return {
      form: this.$inertia.form({
        current_password: '',
        password: '',
        password_confirmation: '',
      }),
    }
  },
  methods: {
    updatePassword() {
      this.form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => this.form.reset(),
        onError: () => {
          if (this.form.errors.password) {
            this.form.reset('password', 'password_confirmation')
            this.$refs.password.focus()
          }
          if (this.form.errors.current_password) {
            this.form.reset('current_password')
            this.$refs.current_password.focus()
          }
        },
      })
    },
  },
}
</script>
