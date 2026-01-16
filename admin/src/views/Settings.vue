<template>
  <div class="settings">
    <el-card>
      <el-tabs v-model="activeTab" class="settings-tabs">
        <el-tab-pane label="基本设置" name="basic">
          <el-form
            ref="basicFormRef"
            :model="basicForm"
            :rules="basicRules"
            label-width="120px"
            class="settings-form"
          >
            <el-form-item label="系统名称" prop="site_name">
              <el-input v-model="basicForm.site_name" placeholder="请输入系统名称" />
            </el-form-item>
            <el-form-item label="系统描述" prop="site_description">
              <el-input
                v-model="basicForm.site_description"
                type="textarea"
                :rows="3"
                placeholder="请输入系统描述"
              />
            </el-form-item>
            <el-form-item label="联系电话" prop="contact_phone">
              <el-input v-model="basicForm.contact_phone" placeholder="请输入联系电话" />
            </el-form-item>
            <el-form-item label="联系邮箱" prop="contact_email">
              <el-input v-model="basicForm.contact_email" placeholder="请输入联系邮箱" />
            </el-form-item>
            <el-form-item label="公司地址" prop="company_address">
              <el-input v-model="basicForm.company_address" placeholder="请输入公司地址" />
            </el-form-item>
            <el-form-item label="备案信息" prop="icp_number">
              <el-input v-model="basicForm.icp_number" placeholder="请输入备案信息" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" :loading="basicLoading" @click="handleBasicSubmit">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
        
        <el-tab-pane label="上传设置" name="upload">
          <el-form
            ref="uploadFormRef"
            :model="uploadForm"
            :rules="uploadRules"
            label-width="120px"
            class="settings-form"
          >
            <el-form-item label="允许上传类型" prop="allowed_types">
              <el-select
                v-model="uploadForm.allowed_types"
                multiple
                placeholder="请选择允许上传的文件类型"
                style="width: 100%"
              >
                <el-option label="图片文件" value="jpg,jpeg,png,gif,bmp,webp" />
                <el-option label="文档文件" value="pdf,doc,docx,xls,xlsx,ppt,pptx" />
                <el-option label="压缩文件" value="zip,rar,7z,tar,gz" />
                <el-option label="音频文件" value="mp3,wav,flac,aac" />
                <el-option label="视频文件" value="mp4,avi,mkv,mov,wmv" />
              </el-select>
            </el-form-item>
            <el-form-item label="最大文件大小" prop="max_file_size">
              <el-input-number
                v-model="uploadForm.max_file_size"
                :min="1"
                :max="100"
                :step="1"
                controls-position="right"
              />
              <span class="unit">MB</span>
            </el-form-item>
            <el-form-item label="图片最大宽度" prop="image_max_width">
              <el-input-number
                v-model="uploadForm.image_max_width"
                :min="100"
                :max="5000"
                :step="100"
                controls-position="right"
              />
              <span class="unit">像素</span>
            </el-form-item>
            <el-form-item label="图片最大高度" prop="image_max_height">
              <el-input-number
                v-model="uploadForm.image_max_height"
                :min="100"
                :max="5000"
                :step="100"
                controls-position="right"
              />
              <span class="unit">像素</span>
            </el-form-item>
            <el-form-item label="是否压缩图片" prop="enable_compression">
              <el-switch v-model="uploadForm.enable_compression" />
            </el-form-item>
            <el-form-item label="压缩质量" prop="compression_quality" v-if="uploadForm.enable_compression">
              <el-slider
                v-model="uploadForm.compression_quality"
                :min="10"
                :max="100"
                :step="5"
                show-input
                show-stops
              />
              <div class="slider-tip">数值越高，图片质量越好，文件越大</div>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" :loading="uploadLoading" @click="handleUploadSubmit">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
        
        <el-tab-pane label="API设置" name="api">
          <el-form
            ref="apiFormRef"
            :model="apiForm"
            :rules="apiRules"
            label-width="120px"
            class="settings-form"
          >
            <el-form-item label="API密钥" prop="api_key">
              <el-input v-model="apiForm.api_key" disabled>
                <template #append>
                  <el-button @click="handleGenerateApiKey">重新生成</el-button>
                </template>
              </el-input>
            </el-form-item>
            <el-form-item label="请求频率限制" prop="rate_limit">
              <el-input-number
                v-model="apiForm.rate_limit"
                :min="10"
                :max="10000"
                :step="10"
                controls-position="right"
              />
              <span class="unit">次/小时</span>
            </el-form-item>
            <el-form-item label="IP白名单" prop="ip_whitelist">
              <el-input
                v-model="apiForm.ip_whitelist"
                type="textarea"
                :rows="3"
                placeholder="请输入允许访问的IP地址，多个IP用逗号分隔"
              />
              <div class="form-tip">留空表示不限制IP访问</div>
            </el-form-item>
            <el-form-item label="CORS域名" prop="cors_domains">
              <el-input
                v-model="apiForm.cors_domains"
                type="textarea"
                :rows="3"
                placeholder="请输入允许跨域访问的域名，多个域名用逗号分隔"
              />
              <div class="form-tip">留空表示不限制跨域访问</div>
            </el-form-item>
            <el-form-item>
              <el-button type="primary" :loading="apiLoading" @click="handleApiSubmit">
                保存设置
              </el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
        
        <el-tab-pane label="邮件设置" name="email">
          <el-form
            ref="emailFormRef"
            :model="emailForm"
            :rules="emailRules"
            label-width="120px"
            class="settings-form"
          >
            <el-form-item label="SMTP服务器" prop="smtp_host">
              <el-input v-model="emailForm.smtp_host" placeholder="请输入SMTP服务器地址" />
            </el-form-item>
            <el-form-item label="SMTP端口" prop="smtp_port">
              <el-input-number
                v-model="emailForm.smtp_port"
                :min="1"
                :max="65535"
                :step="1"
                controls-position="right"
              />
            </el-form-item>
            <el-form-item label="SMTP用户名" prop="smtp_username">
              <el-input v-model="emailForm.smtp_username" placeholder="请输入SMTP用户名" />
            </el-form-item>
            <el-form-item label="SMTP密码" prop="smtp_password">
              <el-input
                v-model="emailForm.smtp_password"
                type="password"
                placeholder="请输入SMTP密码"
                show-password
              />
            </el-form-item>
            <el-form-item label="发件人名称" prop="from_name">
              <el-input v-model="emailForm.from_name" placeholder="请输入发件人名称" />
            </el-form-item>
            <el-form-item label="发件人邮箱" prop="from_email">
              <el-input v-model="emailForm.from_email" placeholder="请输入发件人邮箱" />
            </el-form-item>
            <el-form-item label="启用SSL" prop="enable_ssl">
              <el-switch v-model="emailForm.enable_ssl" />
            </el-form-item>
            <el-form-item>
              <el-button type="primary" :loading="emailLoading" @click="handleEmailSubmit">
                保存设置
              </el-button>
              <el-button @click="handleEmailTest">测试邮件</el-button>
            </el-form-item>
          </el-form>
        </el-tab-pane>
      </el-tabs>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { 
  getBasicSettings, 
  updateBasicSettings,
  getUploadSettings,
  updateUploadSettings,
  getApiSettings,
  updateApiSettings,
  getEmailSettings,
  updateEmailSettings,
  testEmailSettings,
  generateApiKey
} from '@/api/settings'

const activeTab = ref('basic')
const basicLoading = ref(false)
const uploadLoading = ref(false)
const apiLoading = ref(false)
const emailLoading = ref(false)

const basicForm = reactive({
  site_name: '',
  site_description: '',
  contact_phone: '',
  contact_email: '',
  company_address: '',
  icp_number: ''
})

const uploadForm = reactive({
  allowed_types: [],
  max_file_size: 10,
  image_max_width: 1920,
  image_max_height: 1080,
  enable_compression: true,
  compression_quality: 85
})

const apiForm = reactive({
  api_key: '',
  rate_limit: 1000,
  ip_whitelist: '',
  cors_domains: ''
})

const emailForm = reactive({
  smtp_host: '',
  smtp_port: 587,
  smtp_username: '',
  smtp_password: '',
  from_name: '',
  from_email: '',
  enable_ssl: true
})

const basicRules = {
  site_name: [{ required: true, message: '请输入系统名称', trigger: 'blur' }],
  contact_email: [
    { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' }
  ],
  contact_phone: [
    { pattern: /^1[3-9]\d{9}$/, message: '请输入正确的手机号码', trigger: 'blur' }
  ]
}

const uploadRules = {
  allowed_types: [{ required: true, message: '请选择允许上传的文件类型', trigger: 'change' }],
  max_file_size: [
    { required: true, message: '请输入最大文件大小', trigger: 'blur' },
    { min: 1, max: 100, type: 'number', message: '文件大小必须在1-100MB之间', trigger: 'blur' }
  ]
}

const apiRules = {
  rate_limit: [
    { required: true, message: '请输入请求频率限制', trigger: 'blur' },
    { min: 10, max: 10000, type: 'number', message: '频率限制必须在10-10000之间', trigger: 'blur' }
  ]
}

const emailRules = {
  smtp_host: [{ required: true, message: '请输入SMTP服务器地址', trigger: 'blur' }],
  smtp_port: [
    { required: true, message: '请输入SMTP端口', trigger: 'blur' },
    { min: 1, max: 65535, type: 'number', message: '端口必须在1-65535之间', trigger: 'blur' }
  ],
  smtp_username: [{ required: true, message: '请输入SMTP用户名', trigger: 'blur' }],
  from_email: [
    { required: true, message: '请输入发件人邮箱', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱地址', trigger: 'blur' }
  ]
}

// 加载设置数据
const loadSettings = async () => {
  try {
    const [basicRes, uploadRes, apiRes, emailRes] = await Promise.all([
      getBasicSettings(),
      getUploadSettings(),
      getApiSettings(),
      getEmailSettings()
    ])
    
    Object.assign(basicForm, basicRes.data)
    Object.assign(uploadForm, uploadRes.data)
    Object.assign(apiForm, apiRes.data)
    Object.assign(emailForm, emailRes.data)
  } catch (error) {
    ElMessage.error('加载设置失败')
    console.error('加载设置失败:', error)
  }
}

// 基本设置提交
const handleBasicSubmit = async () => {
  basicLoading.value = true
  try {
    await updateBasicSettings(basicForm)
    ElMessage.success('基本设置保存成功')
  } catch (error) {
    ElMessage.error('保存失败')
    console.error('保存基本设置失败:', error)
  } finally {
    basicLoading.value = false
  }
}

// 上传设置提交
const handleUploadSubmit = async () => {
  uploadLoading.value = true
  try {
    await updateUploadSettings(uploadForm)
    ElMessage.success('上传设置保存成功')
  } catch (error) {
    ElMessage.error('保存失败')
    console.error('保存上传设置失败:', error)
  } finally {
    uploadLoading.value = false
  }
}

// API设置提交
const handleApiSubmit = async () => {
  apiLoading.value = true
  try {
    await updateApiSettings(apiForm)
    ElMessage.success('API设置保存成功')
  } catch (error) {
    ElMessage.error('保存失败')
    console.error('保存API设置失败:', error)
  } finally {
    apiLoading.value = false
  }
}

// 邮件设置提交
const handleEmailSubmit = async () => {
  emailLoading.value = true
  try {
    await updateEmailSettings(emailForm)
    ElMessage.success('邮件设置保存成功')
  } catch (error) {
    ElMessage.error('保存失败')
    console.error('保存邮件设置失败:', error)
  } finally {
    emailLoading.value = false
  }
}

// 测试邮件
const handleEmailTest = async () => {
  try {
    await testEmailSettings()
    ElMessage.success('测试邮件发送成功')
  } catch (error) {
    ElMessage.error('测试邮件发送失败')
    console.error('测试邮件失败:', error)
  }
}

// 生成API密钥
const handleGenerateApiKey = async () => {
  try {
    const res = await generateApiKey()
    apiForm.api_key = res.data.api_key
    ElMessage.success('API密钥生成成功')
  } catch (error) {
    ElMessage.error('生成失败')
    console.error('生成API密钥失败:', error)
  }
}

onMounted(() => {
  loadSettings()
})
</script>

<style scoped>
.settings {
  padding: 0;
}

.settings-tabs {
  min-height: 500px;
}

.settings-form {
  max-width: 600px;
  margin: 20px 0;
}

.unit {
  margin-left: 10px;
  color: #909399;
}

.form-tip {
  margin-top: 5px;
  color: #909399;
  font-size: 12px;
}

.slider-tip {
  margin-top: 5px;
  color: #909399;
  font-size: 12px;
}

:deep(.el-input-number) {
  width: 120px;
}

:deep(.el-slider) {
  width: 300px;
}
</style>