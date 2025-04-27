<template>
  <AppLayout title="Missing Features Analysis">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Missing Features Analysis
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div v-if="loading" class="text-center py-6">
              <div class="spinner"></div>
              <p class="mt-2 text-gray-600">Analyzing your usage patterns...</p>
            </div>

            <div v-else>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Feature Recommendations</h3>
              
              <div v-if="features.length === 0" class="text-center py-6 text-gray-500">
                No feature recommendations available. Keep using the platform to get personalized suggestions.
              </div>
              
              <div v-else class="space-y-6">
                <div 
                  v-for="(feature, index) in features" 
                  :key="index"
                  class="bg-gray-50 p-4 rounded-lg border border-gray-200"
                >
                  <h4 class="font-medium text-indigo-600 mb-2">{{ feature.name }}</h4>
                  <p class="text-gray-700 mb-3">{{ feature.description }}</p>
                  <div class="flex items-center text-sm text-gray-500">
                    <span 
                      class="inline-block w-2 h-2 rounded-full mr-2"
                      :class="{
                        'bg-green-500': feature.relevance > 80,
                        'bg-yellow-500': feature.relevance > 50 && feature.relevance <= 80,
                        'bg-red-500': feature.relevance <= 50
                      }"
                    ></span>
                    <span>Relevance: {{ feature.relevance }}%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  components: {
    AppLayout,
  },
  
  setup() {
    const loading = ref(true);
    const features = ref([]);
    const error = ref(null);
    
    const fetchFeatures = async () => {
      try {
        loading.value = true;
        const response = await axios.get(route('features.missing'));
        features.value = response.data.features || [];
      } catch (err) {
        console.error('Error fetching features:', err);
        error.value = 'Failed to load feature recommendations';
      } finally {
        loading.value = false;
      }
    };
    
    onMounted(() => {
      fetchFeatures();
    });
    
    return {
      loading,
      features,
      error
    };
  }
};
</script>

<style scoped>
.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border-left-color: #4f46e5;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>