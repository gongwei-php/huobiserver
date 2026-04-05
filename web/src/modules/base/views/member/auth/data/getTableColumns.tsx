/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://github.com/mineadmin
 */
import type { MaProTableColumns, MaProTableExpose } from '@mineadmin/pro-table'
import { type MemberAuthVo } from '~/base/api/memberauth.ts'
import type { UseDialogExpose } from '@/hooks/useDialog.ts'

import { ElTag } from 'element-plus'
import { useMessage } from '@/hooks/useMessage.ts'
import { ResultCode } from '@/utils/ResultCode.ts'
import hasAuth from '@/utils/permission/hasAuth.ts'
import { agree, refuse } from '~/base/api/memberauth.ts'

export default function getTableColumns(dialog: UseDialogExpose, formRef: any, t: any): MaProTableColumns[] {

  const dictStore = useDictStore()
  const msg = useMessage()

  const showBtn = (auth: string | string[]) => {
    return hasAuth(auth)
  }

  return [
    // 多选列
    {
      type: 'selection', showOverflowTooltip: false, label: () => t('crud.selection'),
      cellRender: ({ row }): any => row.id === 1 ? '-' : undefined,
      selectable: (row: MemberAuthVo) => ![1].includes(row.id as number),
    },
    // 索引序号列
    { type: 'index' },
    // 普通列
    { label: () => t('baseMemberAuthManage.user_id'), prop: 'user_id' },
    {
      label: () => t('baseMemberAuthManage.user_id'), prop: 'user_id',
      cellRender: ({ row }) => (
        <el-image
          style="width: 100px; height: 100px"
          src={(row.card_front_url === '' || !row.card_front_url) ? '' : row.card_front_url}
          zoom-rate="1.2"
          max-scale="7"
          min-scale="0.2"
          preview-src-list="srcList"
          show-progress
          initial-index="4"
          fit="cover"
        />
      ),
    },
    { label: () => t('baseMemberAuthManage.user_id'), prop: 'user_id' },
    {
      label: () => t('crud.status'), prop: 'status',
      cellRender: ({ row }) => (
        <ElTag type={dictStore.t('system-status', row.status, 'color')}>
          {t(dictStore.t('system-status', row.status, 'i18n'))}
        </ElTag>
      ),
    },
    // 操作列
    {
      type: 'operation',
      label: () => t('crud.operation'),
      operationConfigure: {
        type: 'tile',
        actions: [
          {
            name: 'agree',
            icon: 'material-symbols:person-edit',
            show: () => showBtn('member:auth:agree'),
            text: () => t('baseMemberAuthManage.agree'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('baseMemberAuthManage.agreeMessage')).then(async () => {
                const response = await agree(row.id)
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('baseMemberAuthManage.agreeSuccess'))
                  await proxy.refresh()
                }
              })
            },
          },
          {
            name: 'refuse',
            show: () => showBtn('member:auth:refuse'),
            icon: 'mdi:delete',
            text: () => t('baseMemberAuthManage.refuse'),
            onClick: ({ row }, proxy: MaProTableExpose) => {
              msg.delConfirm(t('baseMemberAuthManage.refuseMessage')).then(async () => {
                const response = await refuse(row.id)
                if (response.code === ResultCode.SUCCESS) {
                  msg.success(t('baseMemberAuthManage.refuseSuccess'))
                  await proxy.refresh()
                }
              })
            },
          },
        ],
      },
    },
  ]
}
