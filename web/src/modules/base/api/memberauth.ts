
import type { PageList, ResponseStruct } from '#/global'

export interface MemberAuthVo {
    id?: number
    user_id?: number
    card_front_url?: string
    card_back_url?: string
    status?: 1 | 2
    updated_by?: number
}

export interface MemberAuthSearchVo {
    user_id?: number
    [key: string]: any
}

export function page(data: MemberAuthSearchVo): Promise<ResponseStruct<PageList<MemberAuthVo>>> {
    return useHttp().get('/admin/member/auth/list', { params: data })
}

export function agree(id: number): Promise<ResponseStruct<null>> {
    return useHttp().put(`/admin/member/auth/agree/${id}`)
}

export function refuse(id: number): Promise<ResponseStruct<null>> {
    return useHttp().put(`/admin/member/auth/refuse/${id}`)
}

export function agreeByIds(ids: number[]): Promise<ResponseStruct<null>> {
    return useHttp().post('/admin/member/auth/agree/all', { ids: ids })
}

export function refuseByIds(ids: number[]): Promise<ResponseStruct<null>> {
    return useHttp().post('/admin/member/auth/refuse/all', { ids: ids })
}
