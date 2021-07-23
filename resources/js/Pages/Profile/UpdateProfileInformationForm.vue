<template>
  <profile-form @submitted="updateProfileInformation">
    <template #title>
      アカウント情報
    </template>

    <template #description>
      アカウント情報とメールアドレスの更新をします。
    </template>

    <template #form>
      <!-- Profile Photo -->
      <div class="mb-6">
        <!-- Profile Photo File Input -->
        <input
          type="file"
          v-show="false"
          ref="photo"
          @change="updatePhotoPreview"
        />

        <!-- Current Profile Photo -->
        <div class="text-subtitle-1 mb-1">アイコン</div>

        <div class="mb-2" v-show="!photoPreview">
          <v-avatar size="75">
            <img :src="user.profile_photo_url" :alt="user.name" />
          </v-avatar>
        </div>

        <div class="mb-2" v-show="photoPreview">
          <v-avatar size="75">
            <img :src="photoPreview" alt="新しいアイコン" />
          </v-avatar>
        </div>

        <v-btn outlined @click.prevent="selectNewPhoto">
          新しいアイコン選択
        </v-btn>

        <v-btn
          outlined
          @click.prevent="deletePhoto"
          v-if="user.profile_photo_path"
        >
          アイコン削除
        </v-btn>

        <!-- バリデーションエラー表示 -->
        <div v-if="form.errors.photo" class="ml-n10 ml-md-0">
          <v-alert
            color="error"
            border="left"
            dense
            class="white--text ml-n5 ml-md-0"
            elevation="2"
          >
            {{ form.errors.photo }}
          </v-alert>
        </div>
      </div>

      <!-- Name -->
      <div class="mb-2">
        <v-row no-gutters>
          <v-col cols="12" sm="9" md="7">
            <v-text-field
              id="name"
              v-model="form.name"
              autocomplete="name"
              label="名前"
              outlined
              dense
            ></v-text-field>
          </v-col>

          <v-col cols="12" sm="9" md="7" class="mt-n4">
            <!-- バリデーションエラー表示 -->
            <div v-if="form.errors.name">
              <v-alert
                color="error"
                border="left"
                dense
                class="white--text"
                elevation="2"
              >
                {{ form.errors.name }}
              </v-alert>
            </div>
          </v-col>
        </v-row>
      </div>

      <!-- Email -->
      <div class="mb-2">
        <v-row no-gutters>
          <v-col cols="12" sm="9" md="7">
            <v-text-field
              id="email"
              type="email"
              v-model="form.email"
              autocomplete="email"
              label="メールアドレス"
              dense
              outlined
            ></v-text-field>
          </v-col>

          <v-col cols="12" sm="9" md="7">
            <!-- バリデーションエラー表示 -->
            <div v-if="form.errors.email" class="mt-n4">
              <v-alert
                color="error"
                border="left"
                dense
                class="white--text"
                elevation="2"
              >
                {{ form.errors.email }}
              </v-alert>
            </div>
          </v-col>
        </v-row>
      </div>
    </template>

    <template #actions>
      <span class="text-subtitle-2 mr-2" v-show="form.recentlySuccessful"
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
  props: {
    user: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      form: this.$inertia.form({
        _method: 'PUT',
        name: this.user.name,
        email: this.user.email,
        photo: null,
      }),
      photoPreview: null,
    }
  },
  methods: {
    updateProfileInformation() {
      if (this.$refs.photo) {
        this.form.photo = this.$refs.photo.files[0]
      }
      this.form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
      })
    },
    selectNewPhoto() {
      this.$refs.photo.click()
    },
    updatePhotoPreview() {
      const reader = new FileReader()
      reader.onload = e => {
        this.photoPreview = e.target.result
      }
      reader.readAsDataURL(this.$refs.photo.files[0])
    },
    deletePhoto() {
      this.$inertia.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => (this.photoPreview = null),
      })
    },
  },
}
</script>
