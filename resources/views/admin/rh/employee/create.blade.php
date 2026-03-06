@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Ajouter un Personnel</h5>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('employeeStore') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body" style="background-color:#6c757d;">
                
                {{-- Affichage des erreurs de validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 20px;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    {{-- Colonne 1 : Informations Professionnelles et Contact --}}
                    <div class="col-md-6 border-right">
                        <div class="mb-3 text-white">
                            <label class="fw-bold">Nom et Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3 text-white">
                                <label class="fw-bold">Téléphone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Ex: +223 00000000" required>
                            </div>
                            <div class="col-md-6 mb-3 text-white">
                                <label class="fw-bold">Adresse Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="exemple@mail.com" required>
                            </div>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Fonction / Service <span class="text-danger">*</span></label>
                            <input type="text" name="position" class="form-control" value="{{ old('position') }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Diplôme <span class="text-danger">*</span></label>
                            <select name="diploma" class="form-control" required>
                                <option value="">-- Sélectionner un diplôme --</option>
                                @foreach($diplomas as $d)
                                    <option value="{{ $d }}" {{ old('diploma') == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Spécialité <span class="text-danger">*</span></label>
                            <input type="text" name="specialty" class="form-control" value="{{ old('specialty') }}" placeholder="Ex: Informatique, Comptabilité..." required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Contact d'urgence (Nom & Tél) <span class="text-danger">*</span></label>
                            <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}" placeholder="Ex: Jean Dupont - 77000000" required>
                        </div>
                    </div>

                    {{-- Colonne 2 : Identité, Situation Personnelle et Photo --}}
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-7 mb-3 text-white">
                                <label class="fw-bold">Situation matrimoniale <span class="text-danger">*</span></label>
                                <select name="marital_status" class="form-control" required>
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($maritalStatuses as $status)
                                        <option value="{{ $status }}" {{ old('marital_status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 mb-3 text-white">
                                <label class="fw-bold">Nombre d'enfants <span class="text-danger">*</span></label>
                                <input type="number" name="children_count" class="form-control" value="{{ old('children_count', 0) }}" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Type de pièce <span class="text-danger">*</span></label>
                            <select name="id_type" class="form-control" onchange="toggleOther(this)" required>
                                <option value="CIB" {{ old('id_type') == 'CIB' ? 'selected' : '' }}>CIB</option>
                                <option value="Passeport" {{ old('id_type') == 'Passeport' ? 'selected' : '' }}>Passeport</option>
                                <option value="Autres" {{ old('id_type') == 'Autres' ? 'selected' : '' }}>Autres</option>
                            </select>
                        </div>

                        <div id="other_div" class="mb-3 text-white" style="{{ old('id_type') == 'Autres' ? 'display:block;' : 'display:none;' }}">
                            <label class="fw-bold">Précisez le type <span class="text-danger">*</span></label>
                            <input type="text" name="id_type_other" class="form-control" value="{{ old('id_type_other') }}">
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Numéro de pièce <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" class="form-control" value="{{ old('id_number') }}" required>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Années d'expérience <span class="text-danger">*</span></label>
                            <select name="experience_years" class="form-control" required>
                                <option value="">-- Sélectionner l'expérience --</option>
                                @foreach($experienceLevels as $exp)
                                    <option value="{{ $exp }}" {{ old('experience_years') == $exp ? 'selected' : '' }}>{{ $exp }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 text-white">
                            <label class="fw-bold">Photo de profil</label>
                            <input type="file" name="photo" class="form-control">
                            <small class="text-white-50">Formats acceptés : JPG, PNG (Max: 2Mo)</small>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="mt-4 row">
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white shadow-sm" style="background-color:rgb(249, 138, 27); border:none; font-weight:bold;">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('employeeList') }}" class="btn w-100 btn-secondary shadow-sm" style="font-weight:bold; background-color: rgb(144, 142, 140); border:none;">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * Gère l'affichage du champ "Autres" pour le type de pièce
 */
function toggleOther(select) {
    const div = document.getElementById('other_div');
    const input = div.querySelector('input');
    if (select.value === 'Autres') {
        div.style.display = 'block';
        input.required = true;
    } else {
        div.style.display = 'none';
        input.required = false;
        input.value = '';
    }
}
</script>
@endsection