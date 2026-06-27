<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CompanyCollection;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function companies()
    {
         return new CompanyCollection(Company::where('user_id', auth('api')->user()->id)->orderBy("favorite", "desc")->get());
    }

    public function createCompany(Request $request)
    {
        $path_docs = public_path().'/docs/';
        $path_camara = public_path().'/camara/';
        $path_ruts = public_path().'/ruts/';

        $docfile = '';
        $camarafile = '';
        $rutfile = '';

        $company = new Company([
            'user_id' => auth('api')->user()->id,
            'person_type' => $request->personType,
            'first_name' => $request->firstName,
            'second_name' => $request->secondName,
            'first_lastname' => $request->firstLastname,
            'second_lastname' => $request->secondLastname,
            'document_type' => $request->documentType,
            'document_number' => $request->documentNumber,
            'company_razon' => $request->companyRazon,
            'company_email' => $request->companyEmail,
            'company_phone' => $request->companyPhone,
            'company_actividad' => json_encode($request->companyActividad),
            'regimen_fiscal' => json_encode($request->regimenFiscal),
            'responsabilidad_tribut' => json_encode($request->responsabilidadTribut),
            'company_type' => $request->companyType,
            'company_document_type' => $request->companyDocumentType,
            'company_document_number' => $request->companyDocumentNumber,
            'documento_file' => $docfile,
            'camara_file' => $camarafile,
            'rut_file' => $rutfile,
        ]);

        $company->save();

        return response()->json([
            'success' => true,
            'message' => translate('Company has been added successfully.')
        ]); 
    }


    public function updateCompany(Request $request)
    {
        $path_docs = public_path().'/docs/';
        $path_camara = public_path().'/camara/';
        $path_ruts = public_path().'/ruts/';

        $docfile = '';
        $camarafile = '';
        $rutfile = '';

        $company = Company::findOrFail($request->id);
        if (auth('api')->user()->id != $company->user_id) {
            return response()->json(null, 401);
        }

        $company->person_type = $request->person_type;
        $company->first_name = $request->first_name;
        $company->second_name = $request->second_name;
        $company->first_lastname = $request->first_lastname;
        $company->second_lastname = $request->second_lastname;
        $company->document_type = $request->document_type;
        $company->document_number = $request->document_number;
        $company->company_razon = $request->company_razon;
        $company->company_email = $request->company_email;
        $company->company_phone = $request->company_phone;
        $company->company_actividad = json_encode($request->company_actividad);
        $company->regimen_fiscal = json_encode($request->regimen_fiscal);
        $company->responsabilidad_tribut = json_encode($request->responsabilidad_tribut);
        $company->company_actividad = $request->company_actividad;
        $company->company_type = $request->company_type;
        $company->company_document_type = $request->company_document_type;
        $company->company_document_number = $request->company_document_number;
        $company->documento_file = $docfile;
        $company->camara_file = $camarafile;
        $company->rut_file = $rutfile;
    
        $company->save();

        return response()->json([
            'success' => true,
            'message' => translate('Company has been updated successfully.')
        ]);
    }

    public function deleteCompany($id){
        $company = Company::findOrFail($id);
        if (auth('api')->user()->id != $company->user_id) {
            return response()->json(null, 401);
        }

        $company->delete();

        return response()->json([
            'success' => true,
            'message' => translate('Company has been deleted successfully.'),
        ]);

    }

    public function setFavorite(Request $request){
        $company = Company::findOrFail($request->id);
        if (auth('api')->user()->id != $company->user_id) {
            return response()->json(null, 401);
        }

        $company->favorite = $request->favorite;
        $company->update();

        if($request->favorite == 1)
            return response()->json([
                'success' => true,
                'message' => translate('Company has been successfully placed as a favorite.'),
            ]);
        else
            return response()->json([
                'success' => true,
                'message' => translate('Company has been successfully removed as a favorite.'),
            ]);

    }
}
