{{-- Edit Payment Request Modal — shared by Payment.blade.php and applicant_requests.blade.php --}}
<div x-show="isModalOpen" style="display:none;background:rgba(10,40,77,0.6);backdrop-filter:blur(6px)"
    class="fixed inset-0 z-[70] flex items-center justify-center p-4" @keydown.escape.window="closeModal()"
    @click.self="closeModal()">
    <div class="bg-white flex flex-col overflow-hidden w-full"
        style="max-width:680px;max-height:94vh;border-radius:1.25rem;box-shadow:0 25px 60px rgba(0,0,0,.25)">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between flex-shrink-0"
            style="background:#F0B43C;padding:1.1rem 1.5rem;border-radius:1.25rem 1.25rem 0 0">
            <div class="flex items-center gap-3">
                <div style="width:40px;height:40px;background:rgba(255,255,255,0.12);border-radius:.75rem;display:flex;align-items:center;justify-content:center">
                    <i class="bi bi-pencil-square" style="color:white;font-size:1.1rem"></i>
                </div>
                <div>
                    <h2 style="color:white;font-size:1.05rem;font-weight:700;margin:0">Wax ka Beddel Codsiga Lacag Bixinta</h2>
                    <p style="color:rgba(255,255,255,.8);font-size:.75rem;margin:0">Cusboonaysii macluumaadka codsiga</p>
                </div>
            </div>
            <button @click="closeModal()"
                style="width:34px;height:34px;background:rgba(255,255,255,0.12);border:none;border-radius:.625rem;color:white;cursor:pointer;display:flex;align-items:center;justify-content:center"
                onmouseover="this.style.background='rgba(255,255,255,0.22)'"
                onmouseout="this.style.background='rgba(255,255,255,0.12)'">
                <i class="bi bi-x-lg" style="font-size:.85rem"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="overflow-y-auto flex-1" style="padding:1.5rem 1.75rem">
            <form @submit.prevent="submitForm">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Magaca Buuxa <span style="color:#ef4444">*</span></label>
                        <input type="text" x-model="f.payer_name" required
                            style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                            onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Taleefan <span style="color:#ef4444">*</span></label>
                        <input type="text" x-model="f.payer_phone" required
                            style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                            onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Imeyl <span style="color:#ef4444">*</span></label>
                        <input type="email" x-model="f.payer_email" required
                            style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                            onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.1rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Magaca Maxkamadaha <span style="color:#ef4444">*</span></label>
                        <select x-model="f.court_id" required
                            style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                            onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <option value="">-- Dooro Maxkamadda --</option>
                            @foreach($courts as $court)
                                <option value="{{ $court->CAI }}">{{ $court->longName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Nooca Adeega <span style="color:#ef4444">*</span></label>
                        <select x-model="f.tariff_id" required
                            style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                            onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <option value="">-- Dooro Adeega --</option>
                            @foreach($tariffs as $tariff)
                                <option value="{{ $tariff->id }}">{{ $tariff->name_so }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;margin-bottom:1.5rem">
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Cadadka Ajuurada <span style="color:#ef4444">*</span></label>
                        <input type="number" step="0.01" min="0" x-model="f.amount" required
                            style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                            onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Tariikhda Lacag Bixinta <span style="color:#ef4444">*</span></label>
                        <input type="date" x-model="f.payment_date" required
                            style="width:100%;padding:.65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;box-sizing:border-box;cursor:pointer;transition:border-color .15s,box-shadow .15s"
                            onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                    </div>
                    <div>
                        <label style="display:block;font-size:.68rem;font-weight:800;color:#374151;text-transform:uppercase;letter-spacing:.07em;margin-bottom:.45rem">
                            Xaalada <span style="color:#ef4444">*</span></label>
                        <select x-model="f.status" required
                            style="width:100%;padding:.65rem 2.25rem .65rem .875rem;font-size:.85rem;border:1.5px solid #d1d5db;border-radius:.625rem;background:#fff;color:#111827;outline:none;appearance:none;cursor:pointer;box-sizing:border-box;transition:border-color .15s,box-shadow .15s"
                            onfocus="this.style.borderColor='#528CBE';this.style.boxShadow='0 0 0 3px rgba(82,140,190,.15)'"
                            onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <option value="Pending">Sugaya</option>
                            <option value="Awaiting Approval">Sugaya Ansaxin</option>
                            <option value="Approved">La Ansaxiyay</option>
                            <option value="Failed">Fashilmay</option>
                        </select>
                    </div>
                </div>

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:1rem;border-top:1.5px solid #f3f4f6">
                    <button type="button" @click="closeModal()"
                        style="padding:.6rem 1.5rem;font-size:.85rem;font-weight:600;color:#374151;border:1.5px solid #e5e7eb;border-radius:.625rem;background:white;cursor:pointer"
                        onmouseover="this.style.background='#f9fafb'"
                        onmouseout="this.style.background='white'">Jooji</button>
                    <button type="submit" :disabled="isSubmitting"
                        style="background:#F0B43C;box-shadow:0 4px 14px rgba(240,180,60,.4);display:flex;align-items:center;gap:.5rem;padding:.6rem 1.75rem;font-size:.85rem;font-weight:700;color:white;border:none;border-radius:.625rem;cursor:pointer"
                        :class="{'opacity-70 cursor-not-allowed':isSubmitting}">
                        <i class="bi bi-check-circle-fill" x-show="!isSubmitting"></i>
                        <i class="bi bi-arrow-repeat" x-show="isSubmitting" style="display:none"></i>
                        <span>Kaydi Isbeddelada</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.paymentEditModalData = function () {
        return {
            isModalOpen: false, isSubmitting: false, currentId: null,
            f: { payer_name: '', payer_phone: '', payer_email: '', court_id: '', tariff_id: '', amount: '', payment_date: '', status: 'Pending' },

            openModal(id, payer_name, payer_phone, payer_email, court_id, tariff_id, amount, payment_date, status) {
                this.currentId = id;
                this.f = { payer_name, payer_phone, payer_email, court_id: String(court_id ?? ''), tariff_id: String(tariff_id ?? ''), amount, payment_date, status };
                this.isModalOpen = true;
            },
            closeModal() {
                this.isModalOpen = false;
                this.currentId = null;
                this.f = { payer_name: '', payer_phone: '', payer_email: '', court_id: '', tariff_id: '', amount: '', payment_date: '', status: 'Pending' };
            },
            async submitForm() {
                this.isSubmitting = true;
                try {
                    const res = await fetch(`{{ url('/finance-payments') }}/${this.currentId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.f),
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        this.closeModal();
                        Swal.fire({ title: 'Guul!', text: data.message, icon: 'success', confirmButtonText: 'Hagaag', confirmButtonColor: '#528CBE' })
                            .then(() => window.location.reload());
                    } else {
                        throw new Error(data.message || 'Cusboonaysiintu waa fashilantay.');
                    }
                } catch (e) {
                    Swal.fire({ title: 'Khalad!', text: e.message, icon: 'error', confirmButtonText: 'Hagaag', confirmButtonColor: '#DC2626' });
                } finally {
                    this.isSubmitting = false;
                }
            },
        };
    };
</script>
