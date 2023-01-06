<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Box\Spout\Common\Entity\Style\Border;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\Color;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


class UserController extends Controller {
    public function __construct() {
        if(!request()->route()) return;
        $this->routeNamespace = Str::before(request()->route()->getName(), '.users');
        View::composer('admin/users/*', function($view)  {
            $viewData = [
                'route_namespace' => $this->routeNamespace,
            ];
            // @HOOK_USERS_VIEW_COMPOSERS
            $view->with($viewData);
        });
        // @HOOK_USERS_CONSTRUCT
    }

    private function getRoles() {
        return Role::when(!auth()->user()->hasRole('Super Admin', 'admin'), function ($qry) { //only Super Admin can add Super Admin
            $qry->where(function ($qry2) {
                $qry2->where('name', '!=', 'Super_Admin')
                    ->orWhere('quard', '!=', 'admin');
            });
        })->get();
    }

    public function index($xlsx = null) {
        $roles = Role::get();
        $usersTable = User::getModel()->getTable();
        $bldQry = User::with('addresses')
            ->where("{$usersTable}.site_id", app()->make('Site')->id);
        $viewData = [];
        if($filters = request()->get('filters')) {
            //BY ROLES
            if(isset($filters['role'])) {
                if($filters['role'] === 'all') {
                    $routeQry = request()->query();
                    unset($routeQry['filters']['role']);
                    return redirect( now_route( queries: $routeQry) );
                }
                $filterRole = (int)$filters['role'];
                $foundRole = $roles->search(function ($item, $key) use($filterRole) {
                    return $item->id === $filterRole;
                });
                if($foundRole !== false) {
                    $bldQry->whereHas('roles', function($qry) use ($filterRole) {
                        $qry->where('id', $filterRole);
                    });
                }
                $viewData['filters']['role'] = $filterRole;
            }
            //END BY ROLES
            //BY TYPE
            if(isset($filters['type'])) {
                $filterType = (string)$filters['type'];
                if($filters['type'] == 'all') {
                    $routeQry = request()->query();
                    unset($routeQry['filters']['type']);
                    return redirect( now_route( queries: $routeQry) );
                }
                switch($filterType) {
                    case 'private':
                        $bldQry->where(function($qry) use ($usersTable) {
                            $qry->whereNull("{$usersTable}.type")
                                ->orWhere("{$usersTable}.type", '')
                                ->orWhere("{$usersTable}.type", 'PRIVATE');
                        });
                        break;
                    case 'company':
                        $bldQry->where("{$usersTable}.type", 'COMPANY');
                        break;
                }
                $viewData['filters']['type'] = $filterType;
            }
            //END BY TYPE

            // @HOOK_USERS_INDEX_FILTERS
        }

        //SEARCH
        if(request()->has('search')) {
            $search = request()->get('search');
            if(is_numeric($search)) {
                $bldQry->whereId((int)$search);
            } else {
                $searchParts = explode(' ', $search);
                $searchParts = array_filter($searchParts, 'trim');
                if(empty($searchParts)) {
                    $routeQry = request()->query();
                    unset($routeQry['search']);
                    return redirect( now_route(queries: $routeQry) );
                }
                $bldQry->whereHas('addresses', function($qry) use ($searchParts) {
                    $qry->whereRaw('0 = 1');
                    foreach($searchParts as $searchPart) {
                        $qry->orWhere("fname", 'LIKE', "%{$searchPart}%");
                        $qry->orWhere("lname", 'LIKE', "%{$searchPart}%");
                        $qry->orWhere("email", 'LIKE', "%{$searchPart}%");
                        $qry->orWhere("phone", 'LIKE', "%{$searchPart}%");
                    }
                });
            }
            $viewData['search'] = $search;
        }
        //END SEARCH

        // @HOOK_USERS_INDEX_END

        if($xlsx) {
            $this->getXLSX( $bldQry->get() );
        }

        $users = $bldQry
            ->orderBy("{$usersTable}.id", 'ASC')
            ->paginate(20)->appends( request()->query() );

        $viewData['users'] = $users;
        $viewData['roles'] = $roles;
        return view('admin/users/users', $viewData);
    }

    private function getXLSX($users) {
        //https://opensource.box.com/spout/
        $writer = WriterEntityFactory::createXLSXWriter();

//            $writer->openToFile($filePath); // write data to a file or to a PHP stream
        $writer->openToBrowser('users.xlsx'); // stream data directly to the browser

        $border = (new BorderBuilder())
            ->setBorderBottom(Color::GREEN, Border::WIDTH_THIN, Border::STYLE_DASHED)
            ->build();

        $style = (new StyleBuilder())
            ->setFontBold()
            ->setBorder($border)
//                ->setFontSize(15)
            ->setFontColor(Color::BLUE)
            ->setShouldWrapText()
            ->setBackgroundColor(Color::YELLOW)
            ->build();
        $writer->addRow(WriterEntityFactory::createRowFromArray(array_values([
            'ID',
            'Name',
            'E-mail',
            'Phone',
            'Postcode',
            'City',
            'Street',
            'Country',
            'Company',
            'Orgnum',
        ]), $style));

        /** add a row at a time */
//            $singleRow = WriterEntityFactory::createRow($cells);
//            $writer->addRow($singleRow);
//
//            /** add multiple rows at a time */
//            $multipleRows = [
//                WriterEntityFactory::createRow($cells),
//                WriterEntityFactory::createRow($cells),
//            ];
//            $writer->addRows($multipleRows);
        foreach($users as $user) {
            if(!($userAddr = $user->addresses->first())) {
                $userAddr = $user->getAddress();
            }

            /** Shortcut: add a row from an array of values */
            $rowFromValues = WriterEntityFactory::createRowFromArray([
                'id' => $user->id,
                'fullname' => $userAddr->fullName,
                'email' => $userAddr->email,
                'phone' => $userAddr->phone,
                'postcode' => $userAddr->postcode,
                'city' => $userAddr->city,
                'street' => $userAddr->street,
                'country' => $userAddr->country,
                'company' => $userAddr->company,
                'orgnum' => $userAddr->orgnum,
            ]);
            $writer->addRow($rowFromValues);
        }

        $writer->close();
        exit;
    }

    public function create() {
        $viewData = [
            'roles' => $this->getRoles(),
        ];
        // @HOOK_USERS_CREATE
        return view('admin/users/user', $viewData);
    }

    public function edit(User $chUser) {
        $viewData = [
            'chUser' => $chUser,
            'roles' => $this->getRoles(),
        ];
        // @HOOK_USERS_EDIT
        return view('admin/users/user', $viewData);
    }

    public function store(UserRequest $request) {
        $validatedData = $request->validated();
        // @HOOK_USERS_STORE_VALIDATE
        $chUser = User::create( array_merge([
            'site_id' => app()->make('Site')->id,
        ], $validatedData));
        // @HOOK_USERS_STORE_INSTANCE
        $chUserAddr = $chUser->getAddress( $validatedData['addr'], forceCreate: true);
        $chUser->roles()->sync( $validatedData['roles'] );
        // @HOOK_USERS_STORE_END
        event( 'user.submited', [$chUser, $validatedData] );
        return redirect()->route($this->routeNamespace.'.users.edit', $chUser)
            ->with('user_success', trans('admin/users/user.created'));
    }

    public function update(User $chUser, UserRequest $request) {
        $validatedData = $request->validated();
        // @HOOK_USERS_UPDATE_VALIDATE
        $chUserAddr = $chUser->getAddress();
        $chUser->update( $validatedData );
        $chUserAddr->update( $validatedData['addr'] );
        $chUser->roles()->sync( $validatedData['roles'] );
        // @HOOK_USERS_UPDATE_END

        event( 'user.submited', [$chUser, $validatedData] );
        if($request->has('action')) {
            return redirect()->route($this->routeNamespace.'.users.index')
                ->with('user_success', trans('admin/users/user.updated'));
        }
        return back()->with('user_success', trans('admin/users/user.updated'));
    }

    public function destroy(User $chUser) {
        // @HOOK_USERS_DESTROY
        $chUser->delete();
        // @HOOK_USERS_DESTROY_END
        return redirect()->route($this->routeNamespace.'.users.index')
            ->with('user_danger', trans('admin/users/user.deleted'));
    }
}
