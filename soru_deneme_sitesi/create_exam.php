<?php
require_once 'header.php';

$stmt = $db->query("SELECT * FROM exam_types WHERE status = 1");
$exam_types = $stmt->fetchAll();
?>

<div class="row">
    <div class="col-12">
        <h2 class="mb-4 text-white fw-bold">Yeni Deneme Oluştur</h2>
        
        <!-- Quick Presets -->
        <div class="mb-4 d-flex gap-2 flex-wrap">
            <button class="btn rounded-pill px-3" style="background: rgba(0, 242, 254, 0.1); border: 1px solid #00f2fe; color: #00f2fe;" onclick="quickPreset(15)"><i class="fa-solid fa-bolt me-2"></i>Hızlı Karma Test (15 Soru)</button>
            <button class="btn rounded-pill px-3" style="background: rgba(243, 85, 218, 0.1); border: 1px solid #f355da; color: #f355da;" onclick="quickPreset(20)"><i class="fa-solid fa-crosshairs me-2"></i>Konu Tarama (20 Soru)</button>
        </div>
        
        <div class="glass-card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="start_exam.php" method="POST" id="examForm">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Sınav Türü Seçin</label>
                        <select name="exam_type_id" id="exam_type" class="form-select" required>
                            <option value="">Seçiniz...</option>
                            <?php foreach($exam_types as $type): ?>
                                <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3" id="section_div" style="display:none;">
                        <label class="form-label fw-bold">2. Alt Sınav Seçin</label>
                        <select name="exam_section_id" id="exam_section" class="form-select" required>
                            <option value="">Önce sınav türü seçin</option>
                        </select>
                    </div>

                    <div class="mb-3" id="lesson_div" style="display:none;">
                        <label class="form-label fw-bold">3. Ders Seçin</label>
                        <select name="lesson_id" id="lesson" class="form-select" required>
                            <option value="">Önce alt sınav seçin</option>
                        </select>
                    </div>

                    <div class="mb-4" id="topics_div" style="display:none;">
                        <label class="form-label fw-bold">4. Konuları ve Soru Sayılarını Belirleyin</label>
                        <div class="alert alert-info small">Bir konudan en fazla 15 soru seçebilirsiniz. İstemediğiniz konuları boş bırakın.</div>
                        <div id="topics_list" class="row">
                            <!-- Konular AJAX ile gelecek -->
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg" id="submit_btn" style="display:none;">Denemeyi Başlat</button>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
document.addEventListener("DOMContentLoaded", () => {
    const examTypeSelect = document.getElementById('exam_type');
    const examSectionSelect = document.getElementById('exam_section');
    const lessonSelect = document.getElementById('lesson');
    const topicsList = document.getElementById('topics_list');
    const sectionDiv = document.getElementById('section_div');
    const lessonDiv = document.getElementById('lesson_div');
    const topicsDiv = document.getElementById('topics_div');
    const submitBtn = document.getElementById('submit_btn');
    const examForm = document.getElementById('examForm');

    function showToast(msg) {
        if(window.showCyberToast) {
            window.showCyberToast(msg, 'magenta');
        } else {
            alert(msg);
        }
    }

    // Yardımcı foksiyon: Fetch data
    async function fetchData(url) {
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            showToast('Sunucu bağlantı hatası!');
            return null;
        }
    }

    // Sınav Türü değiştiğinde
    examTypeSelect.addEventListener('change', async (e) => {
        const typeId = e.target.value;
        sectionDiv.style.display = 'none';
        lessonDiv.style.display = 'none';
        topicsDiv.style.display = 'none';
        submitBtn.style.display = 'none';
        
        if (typeId) {
            sectionDiv.style.display = 'block';
            examSectionSelect.innerHTML = '<option value="">Yükleniyor...</option>';
            const data = await fetchData(`fetch_exam_data.php?action=get_sections&exam_type_id=${typeId}`);
            
            examSectionSelect.innerHTML = '<option value="">Seçiniz...</option>';
            if (data && data.length > 0) {
                data.forEach(sec => {
                    examSectionSelect.innerHTML += `<option value="${sec.id}">${sec.name}</option>`;
                });
            } else {
                examSectionSelect.innerHTML = '<option value="">Bu sınava ait bölüm bulunamadı.</option>';
            }
        }
    });

    // Alt Sınav değiştiğinde
    examSectionSelect.addEventListener('change', async (e) => {
        const sectionId = e.target.value;
        lessonDiv.style.display = 'none';
        topicsDiv.style.display = 'none';
        submitBtn.style.display = 'none';
        
        if (sectionId) {
            lessonDiv.style.display = 'block';
            lessonSelect.innerHTML = '<option value="">Yükleniyor...</option>';
            const data = await fetchData(`fetch_exam_data.php?action=get_lessons&section_id=${sectionId}`);
            
            lessonSelect.innerHTML = '<option value="">Seçiniz...</option>';
            if (data && data.length > 0) {
                data.forEach(les => {
                    lessonSelect.innerHTML += `<option value="${les.id}">${les.name}</option>`;
                });
            } else {
                lessonSelect.innerHTML = '<option value="">Bu bölüme ait ders bulunamadı.</option>';
            }
        }
    });

    // Ders değiştiğinde
    lessonSelect.addEventListener('change', async (e) => {
        const lessonId = e.target.value;
        topicsDiv.style.display = 'none';
        submitBtn.style.display = 'none';
        
        if (lessonId) {
            topicsDiv.style.display = 'block';
            topicsList.innerHTML = '<div class="col-12 text-center text-info"><div class="spinner-border spinner-border-sm" role="status"></div> Konular yükleniyor...</div>';
            const data = await fetchData(`fetch_exam_data.php?action=get_topics&lesson_id=${lessonId}`);
            
            topicsList.innerHTML = '';
            if (data && data.length > 0) {
                data.forEach(top => {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 mb-3';
                    col.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
                            <span class="text-truncate text-white-50 ms-2" title="${top.name}">${top.name}</span>
                            <div class="d-flex align-items-center">
                                <button type="button" class="stepper-btn" onclick="stepVal(this, -1)"><i class="fa-solid fa-minus"></i></button>
                                <input type="number" name="topics[${top.id}]" class="stepper-input topic-input" value="0" min="0" max="15" readonly>
                                <button type="button" class="stepper-btn" onclick="stepVal(this, 1)"><i class="fa-solid fa-plus"></i></button>
                            </div>
                        </div>
                    `;
                    topicsList.appendChild(col);
                });
                submitBtn.style.display = 'block';
            } else {
                topicsList.innerHTML = `<div class="col-12"><div class="alert alert-warning" style="background: rgba(243, 156, 18, 0.1); border-color: #f39c12; color: #f39c12;">Bu derse ait konu bulunamadı.</div></div>`;
            }
        }
    });

    // Validasyon
    examForm.addEventListener('submit', (e) => {
        let totalQuestions = 0;
        const topicInputs = document.querySelectorAll('.topic-input');
        
        topicInputs.forEach(input => {
            const val = parseInt(input.value) || 0;
            if (val > 15) {
                showToast("Bir konudan en fazla 15 soru seçebilirsiniz.");
                e.preventDefault();
                return;
            }
            totalQuestions += val;
        });

        if (totalQuestions === 0) {
            showToast("Lütfen en az bir konudan soru sayısı giriniz!");
            e.preventDefault();
        }
    });

    // Custom Stepper Logic
    window.stepVal = function(btn, step) {
        const input = btn.parentElement.querySelector('input');
        let val = parseInt(input.value) || 0;
        val += step;
        if (val < 0) val = 0;
        if (val > 15) val = 15;
        input.value = val;
    };

    // Yüklenme animasyonu foksiyonu
    function triggerEvent(element, eventName) {
        const event = new Event(eventName);
        element.dispatchEvent(event);
    }

    // Quick Presets Logic (Automated Flow)
    window.quickPreset = async function(total) {
        showToast("Hızlı Şablon uygulanıyor, lütfen bekleyin...");
        
        // 1. Sınav Türü Seçimi (Eğer seçili değilse ilkini seç)
        if (!examTypeSelect.value) {
            if (examTypeSelect.options.length > 1) {
                examTypeSelect.selectedIndex = 1;
                triggerEvent(examTypeSelect, 'change');
                await new Promise(r => setTimeout(r, 500)); // AJAX bekleme süresi
            } else {
                showToast("Sınav türü bulunamadı."); return;
            }
        }

        // 2. Alt Sınav Seçimi
        if (!examSectionSelect.value) {
            if (examSectionSelect.options.length > 1) {
                examSectionSelect.selectedIndex = 1;
                triggerEvent(examSectionSelect, 'change');
                await new Promise(r => setTimeout(r, 500));
            } else {
                showToast("Alt sınav yüklenemedi."); return;
            }
        }

        // 3. Ders Seçimi
        if (!lessonSelect.value) {
            if (lessonSelect.options.length > 1) {
                lessonSelect.selectedIndex = 1;
                triggerEvent(lessonSelect, 'change');
                await new Promise(r => setTimeout(r, 500));
            } else {
                showToast("Ders yüklenemedi."); return;
            }
        }

        const inputs = document.querySelectorAll('.topic-input');
        if (inputs.length === 0) {
            showToast('Konular yüklenemedi, lütfen manuel ders seçiniz.');
            return;
        }

        // 4. Soruları Dağıt
        inputs.forEach(input => input.value = 0);
        let perTopic = Math.floor(total / inputs.length);
        let remainder = total % inputs.length;
        if (perTopic === 0) { perTopic = 1; remainder = 0; }
        
        let assigned = 0;
        for (let i = 0; i < inputs.length; i++) {
            if (assigned >= total) break;
            let amount = perTopic + (i < remainder ? 1 : 0);
            if (amount > 15) amount = 15;
            inputs[i].value = amount;
            assigned += amount;
        }

        // 5. Formu Gönder
        showToast("Şablon hazırlandı, sınava yönlendiriliyorsunuz...");
        setTimeout(() => {
            examForm.submit();
        }, 1000);
    };
});
</script>

<?php require_once 'footer.php'; ?>
