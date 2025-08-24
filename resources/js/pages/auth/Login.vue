<script lang="ts" setup>
import { Form, Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

import InputError from '@/components/InputError.vue';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { AlertTriangle, Eye, EyeOff, LoaderCircle } from 'lucide-vue-next';

const props = defineProps<{
    canResetPassword: boolean;
    defaultPassword?: boolean;
    prefill?: { phone?: string };
}>();

const phone = ref(props.prefill?.phone ?? '');
const password = ref('');
const showPass = ref(false);

// Debounced partial reload ke GET /login (create) dengan query ?phone=...
let t: number | undefined;
watch(phone, (v) => {
    if (t) clearTimeout(t);
    const val = (v ?? '').trim();
    if (val.length < 6) return;
    t = window.setTimeout(() => {
        router.visit(route('login'), {
            method: 'get',
            data: { phone: val },
            only: ['defaultPassword', 'prefill'],
            preserveState: true,
            replace: true,
        });
    }, 300);
});
</script>

<template>
    <AuthBase description="Masukkan nomor telepon dan kata sandi untuk masuk" title="Masuk ke akun Anda">
        <Head title="Masuk" />

        <!-- ALERT muncul SEBELUM submit jika BE bilang defaultPassword = true -->
        <Alert v-if="props.defaultPassword" class="mb-4 flex items-start gap-2" variant="warning">
            <AlertTriangle class="mt-0.5 h-4 w-4" />
            <AlertDescription class="flex-1">
                Password Anda masih sama dengan nomor telepon. Demi keamanan, segera ganti password di menu settings.
            </AlertDescription>
        </Alert>

        <Form v-slot="{ errors, processing }" :action="route('login')" :reset-on-success="['password']" class="flex flex-col gap-6" method="post">
            <div class="grid gap-6">
                <!-- Phone -->
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

                <!-- Password -->
                <div class="grid gap-2">
                    <Label for="password">Kata Sandi</Label>
                    <div class="relative">
                        <Input
                            id="password"
                            v-model="password"
                            :type="showPass ? 'text' : 'password'"
                            autocomplete="current-password"
                            class="pr-12"
                            name="password"
                            required
                        />
                        <button
                            :class="
                                showPass
                                    ? 'text-yellow-600 hover:bg-yellow-100 hover:text-yellow-700'
                                    : 'text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700'
                            "
                            aria-label="Toggle password"
                            class="absolute inset-y-0 right-0 mr-2 grid place-items-center rounded-md p-1 transition-colors"
                            type="button"
                            @click="showPass = !showPass"
                        >
                            <Eye v-if="!showPass" class="h-4 w-4" />
                            <EyeOff v-else class="h-4 w-4" />
                        </button>
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <!-- Remember -->
                <div class="flex items-center gap-3">
                    <Checkbox id="remember" name="remember" />
                    <Label for="remember">Ingat saya</Label>
                </div>

                <!-- Submit -->
                <Button :disabled="processing" class="mt-2 w-full bg-emerald-600 text-white hover:bg-emerald-700" type="submit">
                    <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
                    <span v-else>Masuk</span>
                </Button>
            </div>
        </Form>
    </AuthBase>
</template>
