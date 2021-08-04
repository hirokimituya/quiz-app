<template>
  <v-card width="251">
    <v-list>
      <v-subheader>ジャンル別</v-subheader>
      <v-divider></v-divider>
      <v-list-item-group v-model="selectedGenre" color="primary">
        <v-list-item v-for="genre in genres.slice(0, 10)" :key="genre.id">
          <v-list-item-content>
            <v-badge inline :content="genre.quiz_count || '0'">
              <v-list-item-title>
                {{
                  genre.name.length >= 10
                    ? genre.name.substr(0, 10) + '...'
                    : genre.name
                }}
              </v-list-item-title>
            </v-badge>
          </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
    <v-card-text class="mt-n3">
      <a @click.stop="genreDialog">
        ジャンル一覧
      </a>
    </v-card-text>

    <!-- Genre List Modal -->
    <v-dialog v-model="genreDialogFlg" max-width="500">
      <v-card>
        <v-list class="mb-3">
          <v-subheader>ジャンル一覧</v-subheader>
          <v-divider></v-divider>
          <v-list-item-group v-model="selectedGenre" color="primary" mandatory>
            <v-list-item v-for="genre in genres" :key="genre.id">
              <v-list-item-content>
                <v-badge inline :content="genre.quiz_count || '0'">
                  <v-list-item-title>
                    {{ genre.name }}
                  </v-list-item-title>
                </v-badge>
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>

        <v-card-actions class="mt-n5 grey lighten-4">
          <v-spacer></v-spacer>

          <v-btn class="my-1" @click="closeModal">
            キャンセル
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-card>
</template>

<script>
import { getUrlParam } from '@/util'

export default {
  props: {
    genres: {
      type: Array,
      required: true,
    },
    genreListId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      selectedGenre: this.genreListId,
      fixed: false,
      genreDialogFlg: false,
    }
  },
  watch: {
    selectedGenre(newVal, oldVale) {
      let selectedGenreTmp = newVal !== undefined ? newVal : oldVale
      let data = {}
      data.genre = this.genres[selectedGenreTmp].id

      for (let param of ['q', 'sort']) {
        let item = getUrlParam(param)
        if (item !== '') {
          data[param] = item
        }
      }

      this.$inertia.get(route('home'), data)
    },
  },
  methods: {
    genreDialog() {
      this.genreDialogFlg = true
    },
    closeModal() {
      this.genreDialogFlg = false
    },
  },
}
</script>
