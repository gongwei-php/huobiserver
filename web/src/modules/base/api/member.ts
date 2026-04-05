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

export interface MemberVo {
  id?: number
  vip_level_id?: number
  account?: string
  phone?: string
  avatar?: string
  status?: 1 | 2
  login_ip?: string
  login_time?: string
  remark?: string
  password?: string
}

export interface MemberSearchVo {
  account?: String
  phone?: string
  status?: number
}

export function page(data: MemberSearchVo): Promise<ResponseStruct<PageList<MemberVo>>> {
  return useHttp().get('/admin/member/list', { params: data })
}

export function create(data: MemberVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/member/add', data)
}

export function save(id: number, data: MemberVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/member/update/${id}`, data)
}

export function deleteByIds(ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/member/delete', { data: ids })
}

export function resetPassword(id: number): Promise<ResponseStruct<null>> {
  return useHttp().put('/admin/member/password', { id })
}

export function updateInfo(data: MemberVo): Promise<ResponseStruct<null>> {
  return useHttp().put('/admin/member/info', data)
}
