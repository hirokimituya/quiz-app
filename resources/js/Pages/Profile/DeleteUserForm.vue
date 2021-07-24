<template>
  <v-row>
    <v-col cols="12" md="4">
      <div class="text-h6">
        アカウント削除
      </div>
      <div class="text-subtitle-2 mt-3">
        アカウントを完全に削除します。
      </div>
    </v-col>

    <v-col cols="12" md="8">
      <v-card color="accent lighten-5">
        <v-card-text class="px-7 pt-7">
          <div>
            アカウントが削除されると、そのすべてのリソースとデータは完全に削除されます。
            アカウントを削除する前に、保持したいデータや情報をダウンロードしてください。
          </div>

          <v-btn
            color="red"
            class="white--text my-3"
            @click.stop="confirmUserDeletion"
          >
            アカウント削除
          </v-btn>

          <!-- Delete Account Confirmation Modal -->
          <v-dialog v-model="confirmingUserDeletion" max-width="700">
            <v-card>
              <v-card-title>
                アカウント削除
              </v-card-title>

              <v-card-text>
                <div class="mb-3">
                  アカウントを削除してもよろしいですか？
                  アカウントが削除されると、そのすべてのリソースとデータは完全に削除されます。
                  アカウントを完全に削除することを確認するには、パスワードを入力してください。
                </div>

                <v-row no-gutters>
                  <v-col cols="12" sm="8">
                    <v-text-field
                      ref="password"
                      v-model="form.password"
                      type="password"
                      placeholder="パスワード"
                      outlined
                      dense
                      @keyup.enter="deleteUser"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" sm="8" class="mt-n5">
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
              </v-card-text>

              <v-card-actions class="mt-n5 grey lighten-4">
                <v-spacer></v-spacer>

                <v-btn class="my-1" @click="closeModal">
                  キャンセル
                </v-btn>

                <v-btn
                  color="red"
                  class="white--text my-1"
                  :disabled="form.processing"
                  @click="deleteUser"
                >
                  アカウント削除
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  components: {},
  data() {
    return {
      confirmingUserDeletion: false,
      form: this.$inertia.form({
        password: '',
      }),
    }
  },
  methods: {
    confirmUserDeletion() {
      this.confirmingUserDeletion = true
      setTimeout(() => this.$refs.password.focus(), 250)
    },
    deleteUser() {
      this.form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => this.closeModal(),
        onError: () => this.$refs.password.focus(),
        onFinish: () => this.form.reset(),
      })
    },
    closeModal() {
      this.confirmingUserDeletion = false
      this.form.errors.password = false
      this.form.reset()
    },
  },
}
</script>
