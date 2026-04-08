

export default function versionCompare(v1: string, v2: string): number {
  // 将版本号转换成数字数组
  const v1StrArr: string[] = v1.split('.')
  const v2StrArr: string[] = v2.split('.')
  // 对齐版本号的长度
  while (v1StrArr.length < v2StrArr.length) {
    v1StrArr.push('0')
  }
  while (v2StrArr.length < v1StrArr.length) {
    v2StrArr.push('0')
  }
  // 转换成数字数组
  const v1NumArr = v1StrArr.map(Number)
  const v2NumArr = v2StrArr.map(Number)

  for (let i = 0; i < v1NumArr.length; i++) {
    if (v1NumArr[i] < v2NumArr[i]) {
      return -1
    } // v1 < v2
    if (v1NumArr[i] > v2NumArr[i]) {
      return 1
    } // v1 > v2
  }
  return 0 // v1 == v2
}
