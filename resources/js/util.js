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

  return decodeURI(val)
}

/**
 * 小数点以下の桁数を取得する
 * @param {Number} number 対象の数値
 * @return {Number} 小数点以下の桁数
 */
export function getDecimalPointLength(number) {
  var numbers = String(number).split('.');

  return (numbers[1]) ? numbers[1].length : 0;
};

/**
 * 小数点以下2までの文字列に変換する
 * @param {Number} num 対象の数値
 * @return {String} 小数点以下2まである文字列
 */
export function putDesimalPointTwo(num) {
  num = num * 100
  num = Math.round(num) / 100;
  if (getDecimalPointLength(num) == 0) {
    return num + '.00'
  }
  else if  (getDecimalPointLength(num) == 1) {
    return num * '0'
  }
  return num;
} 

export function num2eng(num) {
  switch(num) {
    case 0: return 'zero';
    case 1: return 'one';
    case 2: return 'two';
    case 3: return 'three';
    case 4: return 'four';
    case 5: return 'five';
    case 6: return 'six';
    case 7: return 'seven';
    case 8: return 'eight';
    case 9: return 'nine';
    case 10: return 'ten';
  }
}

export function eng2num(str) {
  switch(str) {
    case 'zero': return 0;
    case 'one': return 1;
    case 'two': return 2;
    case 'three': return 3;
    case 'four': return 4;
    case 'five': return 5;
    case 'six': return 6;
    case 'seven': return 7;
    case 'eight': return 8;
    case 'nine': return 9;
    case 'ten': return 10;
  }
}

export const SORT_ITEMS = [
  { value: 'new', text: '新着順' },
  { value: 'quiz', text: 'クイズ回答回数順' },
  { value: 'like', text: 'いいね数順' },
  { value: 'comment', text: 'コメント数順'},
]
