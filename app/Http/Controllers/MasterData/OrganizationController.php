<?php

namespace App\Http\Controllers\MasterData;

use App\Models\Organization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrganizationByParts;
use App\Services\SatuSehat\OrganizationService;
use Illuminate\Pagination\LengthAwarePaginator;

class OrganizationController extends Controller
{
    protected $organization;
    protected $prefix;
    protected $endpoint;
    protected $view;
    protected $routeIndex;

    public function __construct()
    {
        $this->organization = new OrganizationService();
        $this->prefix = 'Organization';
        $this->endpoint = 'Organization';
        $this->view = 'pages.md.organization.';
        $this->routeIndex = 'organization.index';
    }
    public function index()
    {
        // $organizations = DB::table('organizations')
        //     ->select('organization_id', DB::raw("MAX(SUBSTRING_INDEX(part_of, '/', -1)) AS part_of_id"))
        //     ->groupBy('organization_id')
        //     ->get();
        // dd($organizations);
        $title = $this->prefix . ' ' . 'Index';
        $organizations = Organization::all();
        return view($this->view . 'index', compact('title', 'organizations'));
    }

    public function create()
    {
        $title = 'Create' . ' ' . $this->prefix;
        $organizations = Organization::select('organization_id', 'name')->get();
        return view($this->view . 'create', compact('title', 'organizations'));
    }

    public function store(Request $request)
    {
        $body = [
            'name' => $request->name,
            'part_of' => $request->part_of
        ];

        try {
            // send API 
            $data = $this->organization->postRequest($this->endpoint, $body);

            // Send DB
            $organization =  Organization::create([
                'organization_id' => $data['id'],
                'name' => $data['name'],
                'part_of' => $data['partOf']['reference'] ?? '',
                'created_by' => 'system'
            ]);

            $message = 'New item has been created successfully.';
            if ($request->modal == 'modal') {
                return redirect()->back()->with('toast_success', $message);
            }
            return redirect()->route($this->routeIndex)->with('toast_success', $message);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }

    public function show($organization_id)
    {
        $title = 'Detail' . ' ' . $this->prefix;

        $organization = Organization::where('organization_id', $organization_id)->first();
        $dataById =  $this->organization->getRequest($this->endpoint . '/' . $organization_id);

        $organizationbyParts = $this->organization->getRequest($this->endpoint, [
            'partOf' => $organization_id
        ]);
        $entries =  collect($organizationbyParts['entry']);

        $collection = collect($entries);
        $perPage = 5;

        // Ambil nomor halaman saat ini
        $page = request()->input('page', 1);

        // Potong array entri berdasarkan halaman saat ini dan jumlah item per halaman
        $currentPageItems = $collection->slice(($page - 1) * $perPage, $perPage)->all();

        // Buat objek LengthAwarePaginator
        $organizationbyParts = new LengthAwarePaginator($currentPageItems, count($collection), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view($this->view . 'detail', compact('dataById', 'title', 'organizationbyParts'));
    }

    public function edit($organization_id)
    {
        $title = 'Edit' . ' ' . $this->prefix;
        $organization = Organization::where('organization_id', $organization_id)->first();
        $organizations = Organization::select('organization_id', 'name')->get();
        return view($this->view . 'edit', compact('title', 'organization', 'organizations'));
    }

    public function update($organization_id, Request $request)
    {
        $organization = Organization::where('organization_id', $organization_id)->first();
        $body = [
            'id' => $organization->organization_id,
            'name' => $request->name,
            'part_of' => $request->part_of ?? ''
        ];

        $url = $this->endpoint . '/' . $body['id'];

        try {
            // send API 
            $data = $this->organization->patchRequest($url, $body);

            // Send DB
            $organization->update([
                'organization_id' => $data['id'],
                'name' => $data['name'],
                'part_of' => $data['partOf']['reference'] ?? '',
                'created_by' => 'system'
            ]);

            $message = 'Data has been updated successfully.';
            return redirect()->route($this->routeIndex)->with('toast_success', $message);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data');
        }
    }
}
