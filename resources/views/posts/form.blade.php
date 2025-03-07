<div class="mb-6">
    <label class="block text-gray-700 font-bold mb-2">
        Планирование публикации
    </label>
    <div class="flex items-center gap-4">
        <label class="inline-flex items-center">
            <input type="radio" name="publish_type" value="now" class="form-radio" 
                {{ (!old('publish_type', $post->scheduled_at ? 'scheduled' : 'now') || old('publish_type') === 'now') ? 'checked' : '' }}>
            <span class="ml-2">Опубликовать сейчас</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="publish_type" value="scheduled" class="form-radio"
                {{ old('publish_type', $post->scheduled_at ? 'scheduled' : '') === 'scheduled' ? 'checked' : '' }}>
            <span class="ml-2">Запланировать</span>
        </label>
    </div>
    
    <div id="scheduling-options" class="mt-4 {{ old('publish_type', $post->scheduled_at ? 'scheduled' : '') === 'scheduled' ? '' : 'hidden' }}">
        <input type="datetime-local" 
            name="scheduled_at" 
            class="form-input rounded-md shadow-sm mt-1 block w-full"
            value="{{ old('scheduled_at', $post->scheduled_at ? $post->scheduled_at->format('Y-m-d\TH:i') : '') }}"
            min="{{ now()->format('Y-m-d\TH:i') }}"
        >
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const publishTypeInputs = document.querySelectorAll('input[name="publish_type"]');
    const schedulingOptions = document.getElementById('scheduling-options');

    publishTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.value === 'scheduled') {
                schedulingOptions.classList.remove('hidden');
            } else {
                schedulingOptions.classList.add('hidden');
            }
        });
    });
});
</script>
@endpush 