/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { PageList, ResponseStruct } from '#/global'

export interface MemberVipVo {
  id?: number
  level?: number
  status?: 1 | 2
  updated_by?: number
}

export interface MemberVipSearchVo {
  level?: number
  [key: string]: any
}

export function page(data: MemberVipSearchVo): Promise<ResponseStruct<PageList<MemberVipVo>>> {
  return useHttp().get('/admin/member/vip/list', { params: data })
}

export function create(data: MemberVipVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/member/vip/add', data)
}

export function save(id: number, data: MemberVipVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/member/vip/update/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/member/vip/delete', { data: ids })
}
