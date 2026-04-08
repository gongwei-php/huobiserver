
import type { PageList, ResponseStruct } from '#/global'

export interface LeaderVo {
  user_id?: number | null
  dept_id?: number
  dept_name?: string
}

export interface LeaderSearchVo {
  user_id?: string
  [key: string]: any
}

export function page(data: LeaderSearchVo | null = null): Promise<ResponseStruct<PageList<LeaderVo>>> {
  return useHttp().get('/admin/leader/list', { params: data })
}

export function create(data: LeaderVo): Promise<ResponseStruct<null>> {
  return useHttp().post('/admin/leader', data)
}

export function save(id: number, data: LeaderVo): Promise<ResponseStruct<null>> {
  return useHttp().put(`/admin/leader/${id}`, data)
}

export function deleteByDoubleKey(dept_id: number, user_ids: number[]): Promise<ResponseStruct<null>> {
  return useHttp().delete('/admin/leader', { data: { dept_id, user_ids } })
}
