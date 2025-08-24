<script lang="ts" setup>
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref, watch } from 'vue';

defineProps<{ status?: string; canResetPassword: boolean }>();

const phone = ref('');
watch(phone, (v) => {
    const hidden = document.getElementById('password-hidden') as HTMLInputElement | null;
    if (hidden) hidden.value = v || '';
});
</script>

<template>
    <AuthBase description="Masukkan nomor telepon untuk masuk" title="Masuk ke akun Anda">
        <Head title="Masuk" />
        <div v-if="status" class="mb-4 text-center text-sm font-medium text-emerald-700">{{ status }}</div>

        <Form v-slot="{ errors, processing }" :action="route('login')" :reset-on-success="['password']" class="flex flex-col gap-6" method="post">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="phone">Nomor Telepon</Label>
                    <Input
                        id="phone"
                        v-model="phone"
                        autocomplete="tel"
                        autofocus
                        inputmode="tel"
                        name="phone"
                        placeholder="08xxxxxxxxxx"
                        required
                        type="tel"
                    />
                    <InputError :message="errors.phone" />
                </div>

                <input id="password-hidden" :value="phone" name="password" type="hidden" />

                <div class="flex items-center justify-between">
                    <Label class="flex items-center gap-3" for="remember">
                        <Checkbox id="remember" name="remember" />
                        <span>Ingat saya</span>
                    </Label>
                    <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm">Lupa kata sandi?</TextLink>
                </div>

                <Button :disabled="processing" class="mt-2 w-full bg-emerald-600 text-white hover:bg-emerald-700" type="submit">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    <span v-else>Masuk</span>
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Belum punya akun?
                <TextLink :href="route('register')">Daftar</TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
