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
    return useHttp().put('/admin/member/auth/agree/${id}')
}

export function refuse(id: number): Promise<ResponseStruct<null>> {
    return useHttp().put(`/admin/member/auth/refuse/${id}`)
}
