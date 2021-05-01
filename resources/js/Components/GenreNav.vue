<template>
  <v-card min-width="200">
    <v-list>
      <v-subheader>ジャンル別</v-subheader>
      <v-divider></v-divider>
      <v-list-item-group
        v-model="selectedGenre"
        color="primary"
        mandatory
      >
        <v-list-item
          v-for="genre in genres"
          :key="genre.id"
        >
        <v-list-item-content>
          <v-list-item-title>
            {{ genre.name }}
          </v-list-item-title>
        </v-list-item-content>
        </v-list-item>
      </v-list-item-group>
    </v-list>
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
    }
  },
  data() {
    return {
      selectedGenre: this.genreListId,
      fixed: false,
    }
  },
  watch: {
    selectedGenre() {
      let data = {}
      data.genre = this.genres[this.selectedGenre].id

      let sort = getUrlParam('sort')
      if (sort !== '') {
        data.sort = sort
      }

      this.$inertia.get(route('home'), data)
    }
  },
}
</script>
