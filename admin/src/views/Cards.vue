<template>
  <div class="cards-management">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>名片管理</span>
          <el-button type="primary" @click="handleCreate">
            <el-icon><Plus /></el-icon>
            新建名片
          </el-button>
        </div>
      </template>
      
      <!-- 搜索和筛选 -->
      <div class="search-bar">
        <el-form :inline="true" :model="searchForm" class="search-form">
          <el-form-item label="姓名">
            <el-input
              v-model="searchForm.name"
              placeholder="请输入姓名"
              clearable
              @keyup.enter="handleSearch"
            />
          </el-form-item>
          <el-form-item label="公司">
            <el-input
              v-model="searchForm.company"
              placeholder="请输入公司名称"
              clearable
              @keyup.enter="handleSearch"
            />
          </el-form-item>
          <el-form-item label="职位">
            <el-input
              v-model="searchForm.position"
              placeholder="请输入职位"
              clearable
              @keyup.enter="handleSearch"
            />
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="handleSearch">
              <el-icon><Search /></el-icon>
              搜索
            </el-button>
            <el-button @click="handleReset">
              <el-icon><Refresh /></el-icon>
              重置
            </el-button>
          </el-form-item>
        </el-form>
      </div>
      
      <!-- 数据表格 -->
      <el-table
        v-loading="loading"
        :data="cardsList"
        border
        style="width: 100%"
      >
        <el-table-column type="selection" width="55" />
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="姓名" width="120" />
        <el-table-column prop="position" label="职位" width="150" />
        <el-table-column prop="company" label="公司" min-width="200" />
        <el-table-column prop="phone" label="电话" width="120" />
        <el-table-column prop="email" label="邮箱" width="180" />
        <el-table-column prop="address" label="地址" min-width="200" show-overflow-tooltip />
        <el-table-column prop="created_at" label="创建时间" width="160">
          <template #default="{ row }">
            {{ formatDate(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" link @click="handleEdit(row)">
              编辑
            </el-button>
            <el-button type="danger" link @click="handleDelete(row)">
              删除
            </el-button>
          </template>
        </el-table-column>
      </el-table>
      
      <!-- 分页 -->
      <div class="pagination">
        <el-pagination
          v-model:current-page="pagination.currentPage"
          v-model:page-size="pagination.pageSize"
          :page-sizes="[10, 20, 50, 100]"
          :total="pagination.total"
          layout="total, sizes, prev, pager, next, jumper"
          @size-change="handleSizeChange"
          @current-change="handleCurrentChange"
        />
      </div>
    </el-card>
    
    <!-- 创建/编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="dialogTitle"
      width="600px"
      @close="handleDialogClose"
    >
      <el-form
        ref="cardFormRef"
        :model="cardForm"
        :rules="cardRules"
        label-width="80px"
      >
        <el-form-item label="姓名" prop="name">
          <el-input v-model="cardForm.name" placeholder="请输入姓名" />
        </el-form-item>
        <el-form-item label="职位" prop="position">
          <el-input v-model="cardForm.position" placeholder="请输入职位" />
        </el-form-item>
        <el-form-item label="公司" prop="company">
          <el-input v-model="cardForm.company" placeholder="请输入公司名称" />
        </el-form-item>
        <el-form-item label="电话" prop="phone">
          <el-input v-model="cardForm.phone" placeholder="请输入电话号码" />
        </el-form-item>
        <el-form-item label="邮箱" prop="email">
          <el-input v-model="cardForm.email" placeholder="请输入邮箱地址" />
        </el-form-item>
        <el-form-item label="地址" prop="address">
          <el-input
            v-model="cardForm.address"
            type="textarea"
            :rows="3"
            placeholder="请输入公司地址"
          />
        </el-form-item>
        <el-form-item label="头像" prop="avatar">
          <el-upload
            class="avatar-uploader"
            action="/api/upload/avatar"
            :show-file-list="false"
            :on-success="handleAvatarSuccess"
            :before-upload="beforeAvatarUpload"
          >
            <img v-if="cardForm.avatar" :src="cardForm.avatar" class="avatar" />
            <el-icon v-else class="avatar-uploader-icon"><Plus /></el-icon>
          </el-upload>
        </el-form-item>
      </el-form>
      
      <template #footer>
        <span class="dialog-footer">
          <el-button @click="dialogVisible = false">取消</el-button>
          <el-button type="primary" :loading="submitLoading" @click="handleSubmit">
            确定
          </el-button>
        </span>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus, Search, Refresh } from '@element-plus/icons-vue'
import { getCardsList, createCard, updateCard, deleteCard } from '@/api/cards'

const loading = ref(false)
const submitLoading = ref(false)
const dialogVisible = ref(false)
const dialogTitle = ref('')
const cardsList = ref([])
const cardFormRef = ref()

const searchForm = reactive({
  name: '',
  company: '',
  position: ''
})

const pagination = reactive({
  currentPage: 1,
  pageSize: 10,
  total: 0
})

const cardForm = reactive({
  id: null,
  name: '',
  position: '',
  company: '',
  phone: '',
  email: '',
  address: '',
  avatar: ''
})

const cardRules = {
  name: [
    { required: true, message: '请输入姓名', trigger: 'blur' },
    { min: 2, max: 20, message: '姓名长度在 2 到 20 个字符', trigger: 'blur' }
  ],
  phone: [
    { required: true, message: '请输入电话号码', trigger: 'blur' },
    { pattern: /^1[3-9]\d{9}$/, message: '请输入正确的手机号码', trigger: 'blur' }
  ],
  email: [
    { required: true, message: '请输入邮箱地址', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' }
  ],
  company: [
    { required: true, message: '请输入公司名称', trigger: 'blur' },
    { min: 2, max: 100, message: '公司名称长度在 2 到 100 个字符', trigger: 'blur' }
  ],
  position: [
    { required: true, message: '请输入职位', trigger: 'blur' },
    { min: 2, max: 50, message: '职位长度在 2 到 50 个字符', trigger: 'blur' }
  ]
}

// 获取名片列表
const loadCardsList = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.currentPage,
      page_size: pagination.pageSize,
      ...searchForm
    }
    
    const res = await getCardsList(params)
    cardsList.value = res.data.list
    pagination.total = res.data.total
  } catch (error) {
    ElMessage.error('获取名片列表失败')
    console.error('获取名片列表失败:', error)
  } finally {
    loading.value = false
  }
}

// 搜索
const handleSearch = () => {
  pagination.currentPage = 1
  loadCardsList()
}

// 重置搜索
const handleReset = () => {
  Object.assign(searchForm, {
    name: '',
    company: '',
    position: ''
  })
  handleSearch()
}

// 创建名片
const handleCreate = () => {
  dialogTitle.value = '新建名片'
  dialogVisible.value = true
  resetForm()
}

// 编辑名片
const handleEdit = (row) => {
  dialogTitle.value = '编辑名片'
  dialogVisible.value = true
  Object.assign(cardForm, {
    id: row.id,
    name: row.name,
    position: row.position,
    company: row.company,
    phone: row.phone,
    email: row.email,
    address: row.address,
    avatar: row.avatar
  })
}

// 删除名片
const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除名片 "${row.name}" 吗？`,
      '删除确认',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )
    
    await deleteCard(row.id)
    ElMessage.success('删除成功')
    loadCardsList()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
      console.error('删除失败:', error)
    }
  }
}

// 提交表单
const handleSubmit = async () => {
  if (!cardFormRef.value) return
  
  await cardFormRef.value.validate(async (valid) => {
    if (valid) {
      submitLoading.value = true
      try {
        if (cardForm.id) {
          await updateCard(cardForm.id, cardForm)
          ElMessage.success('更新成功')
        } else {
          await createCard(cardForm)
          ElMessage.success('创建成功')
        }
        
        dialogVisible.value = false
        loadCardsList()
      } catch (error) {
        ElMessage.error(error.message || '操作失败')
        console.error('提交失败:', error)
      } finally {
        submitLoading.value = false
      }
    }
  })
}

// 重置表单
const resetForm = () => {
  Object.assign(cardForm, {
    id: null,
    name: '',
    position: '',
    company: '',
    phone: '',
    email: '',
    address: '',
    avatar: ''
  })
}

// 对话框关闭
const handleDialogClose = () => {
  resetForm()
  cardFormRef.value?.clearValidate()
}

// 分页处理
const handleSizeChange = (val) => {
  pagination.pageSize = val
  loadCardsList()
}

const handleCurrentChange = (val) => {
  pagination.currentPage = val
  loadCardsList()
}

// 头像上传处理
const handleAvatarSuccess = (response) => {
  if (response.code === 200) {
    cardForm.avatar = response.data.url
  } else {
    ElMessage.error('头像上传失败')
  }
}

const beforeAvatarUpload = (file) => {
  const isImage = file.type.startsWith('image/')
  const isLt2M = file.size / 1024 / 1024 < 2

  if (!isImage) {
    ElMessage.error('只能上传图片文件!')
    return false
  }
  if (!isLt2M) {
    ElMessage.error('头像大小不能超过 2MB!')
    return false
  }
  return true
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('zh-CN')
}

onMounted(() => {
  loadCardsList()
})
</script>

<style scoped>
.cards-management {
  padding: 0;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.search-bar {
  margin-bottom: 20px;
  padding: 20px;
  background: #f5f7fa;
  border-radius: 4px;
}

.search-form {
  margin: 0;
}

.pagination {
  margin-top: 20px;
  text-align: right;
}

.avatar-uploader {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  width: 100px;
  height: 100px;
}

.avatar-uploader:hover {
  border-color: #409EFF;
}

.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
  width: 100px;
  height: 100px;
  line-height: 100px;
  text-align: center;
}

.avatar {
  width: 100px;
  height: 100px;
  display: block;
}
</style>