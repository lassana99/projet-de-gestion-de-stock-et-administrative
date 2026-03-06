@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        {{-- En-tête --}}
        <div class="card-header py-3" style="background-color:#6c757d;">
            <h5 class="m-0 font-weight-bold text-white">Modifier le Personnel : {{ $employee->matricule }}</h5>
        </div>

        {{-- Formulaire --}}
        <form action="{{ route('employeeUpdate', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
                    {{-- Colonne Gauche : Informations Professionnelles et Contact --}}
                    <div class="col-md-6 border-right">
                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Matricule</label>
                            <input type="text" class="form-control" value="{{ $employee->matricule }}" readonly 
                                   style="background-color: #e9ecef; font-weight: bold; color: #495057;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Nom et Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" 
                                   value="{{ old('full_name', $employee->full_name) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white fw-bold">Téléphone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" 
                                       value="{{ old('phone', $employee->phone) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-white fw-bold">Adresse Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ old('email', $employee->email) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Fonction / Service <span class="text-danger">*</span></label>
                            <input type="text" name="position" class="form-control" 
                                   value="{{ old('position', $employee->position) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Diplôme <span class="text-danger">*</span></label>
                            <select name="diploma" class="form-control" required>
                                @foreach($diplomas as $d)
                                    <option value="{{ $d }}" {{ old('diploma', $employee->diploma) == $d ? 'selected' : '' }}>{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Spécialité <span class="text-danger">*</span></label>
                            <input type="text" name="specialty" class="form-control" 
                                   value="{{ old('specialty', $employee->specialty) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Contact d'urgence (Nom & Tél) <span class="text-danger">*</span></label>
                            <input type="text" name="emergency_contact" class="form-control" 
                                   value="{{ old('emergency_contact', $employee->emergency_contact) }}" required>
                        </div>
                    </div>

                    {{-- Colonne Droite : Identité, Vie Privée et Photo --}}
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="form-label text-white fw-bold">Situation matrimoniale <span class="text-danger">*</span></label>
                                <select name="marital_status" class="form-control" required>
                                    @foreach($maritalStatuses as $status)
                                        <option value="{{ $status }}" {{ old('marital_status', $employee->marital_status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label text-white fw-bold">Nombre d'enfants <span class="text-danger">*</span></label>
                                <input type="number" name="children_count" class="form-control" 
                                       value="{{ old('children_count', $employee->children_count) }}" min="0" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Type de pièce d'identité <span class="text-danger">*</span></label>
                            <select name="id_type" id="id_type" class="form-control" onchange="toggleIdOther(this)" required>
                                <option value="CIB" {{ old('id_type', $employee->id_type) == 'CIB' ? 'selected' : '' }}>CIB</option>
                                <option value="Passeport" {{ old('id_type', $employee->id_type) == 'Passeport' ? 'selected' : '' }}>Passeport</option>
                                <option value="Autres" {{ old('id_type', $employee->id_type) == 'Autres' ? 'selected' : '' }}>Autres</option>
                            </select>
                        </div>

                        <div class="mb-3" id="id_type_other_div" style="{{ old('id_type', $employee->id_type) == 'Autres' ? 'display:block;' : 'display:none;' }}">
                            <label class="form-label text-white fw-bold">Précisez le type <span class="text-danger">*</span></label>
                            <input type="text" name="id_type_other" id="id_type_other" class="form-control" 
                                   value="{{ old('id_type_other', $employee->id_type_other) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Numéro de pièce <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" class="form-control" 
                                   value="{{ old('id_number', $employee->id_number) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Années d'expérience <span class="text-danger">*</span></label>
                            <select name="experience_years" class="form-control" required>
                                @foreach($experienceLevels as $exp)
                                    <option value="{{ $exp }}" {{ old('experience_years', $employee->experience_years) == $exp ? 'selected' : '' }}>{{ $exp }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-white fw-bold">Photo de profil</label>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if($employee->photo)
                                    <img src="{{ asset('storage/' . $employee->photo) }}" 
                                         alt="Photo" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                        <i class="fa-solid fa-user text-white fa-3x"></i>
                                    </div>
                                @endif
                                <input type="file" name="photo" class="form-control text-white border-0">
                            </div>
                            <small class="text-white-50">Laissez vide pour conserver la photo actuelle.</small>
                        </div>
                    </div>
                </div>

                {{-- Boutons d'action --}}
                <div class="row mt-4">
                    <div class="col-6">
                        <button type="submit" class="btn w-100 text-white shadow-sm" style="background-color: rgb(249, 138, 27); border: none; font-weight: bold;">
                            <i class="fa-solid fa-rotate me-1"></i> Mettre à jour
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('employeeList') }}" class="btn w-100 text-white shadow-sm" style="background-color: #908e8c; border: none; font-weight: bold;">
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
function toggleIdOther(select) {
    const div = document.getElementById('id_type_other_div');
    const input = document.getElementById('id_type_other');
    if(select.value === 'Autres') {
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