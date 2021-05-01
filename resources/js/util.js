/**
 * URLからクエリパラメータの値を取得
 * @param {String} searchKey 検索するキー
 * @return {String} キーに対応する値
 */
export function getUrlParam(searchKey) {
  if (location.search === '' || typeof searchKey === 'undefined') {
    return ''
  }

  let val = ''

  location.search.substr(1).split('&').forEach(param => {
    let [key, value] = param.split('=')
    if (key === searchKey) {
      return val = value
    }
  })

  return val
}
