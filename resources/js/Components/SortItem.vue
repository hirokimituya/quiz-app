<template>
  <v-select
    v-model="selectSortItem"
    :items="sortItems"
    label="ソート"
    outlined
    dense
  ></v-select>
</template>

<script>
import { getUrlParam } from '@/util'

export default {
  props: {
    sortItems: {
      type: Array,
      required: true,
    },
    sortItem: {
      type: String,
      required: true,
    },
    actionPath: {
      type: String,
      required: true,
    },
    actionParam: {
      type: Object,
      required: false,
      default: function() {
        return {}
      },
    },
    attachedUrlParams: {
      type: Array,
      default: function() {
        return []
      },
    },
  },
  data() {
    return {
      selectSortItem: this.sortItem,
    }
  },
  watch: {
    selectSortItem() {
      let data = {}

      let item
      for (let param of this.attachedUrlParams) {
        item = getUrlParam(param)
        if (item !== '') {
          data[param] = item
        }
      }

      data.sort = this.selectSortItem

      this.$inertia.get(route(this.actionPath, this.actionParam), data)
    },
  },
}
</script>
