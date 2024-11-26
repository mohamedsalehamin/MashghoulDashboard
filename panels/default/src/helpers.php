<?php


use App\DefaultPanel\Lib\Breadcrumbs;

function patient() {
    return auth()->user()->patient;
}

function doctor() {
    return auth()->user()?->doctor?->doctor;
}

function provider() {
    return \App\UsersModule\Models\Provider::where('user_id', auth()->id())->first();
}

function site(): object {
    return new class {
        function user() {
            return auth()->guard('site')->user();
        }

        function direction(): string {
            return app()->getLocale() == 'ar' ? 'rtl' : 'ltr';
        }

        function logout() {
            auth()->guard('site')->logout();
        }

        public function breadcrumbs(): Breadcrumbs {
            return new Breadcrumbs();
        }

        public function reservation() {
            return new class {
                public function doctor() {
                    return session()->get('reservation_data')['doctor'];
                }

                public function lab() {
                    return session()->get('reservation_data')['lab'];
                }

                public function formattedDate(): string {
                    return \Carbon\Carbon::parse($this->date())->translatedFormat("D d M Y");
                }

                public function date() {

                    $slot = explode(' - ', $this->slot())[0];
                    $slot=!$slot?'00:00':$slot;
                    return \Carbon\Carbon::parse(session()->get('reservation_data')['date'])->setTimeFromTimeString($slot);
                }

                public function slot() {
                    return session()->get('reservation_data')['slot'];
                }

                public function services() {
                    return app('cart')->getContent();
                }

                public function total() {
                    return app('cart')->formattedTotals()['total'];
                }
            };
        }
    };

}
