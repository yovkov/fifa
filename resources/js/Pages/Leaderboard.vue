<template>
    <Head title="Games" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Leaderboard
            </h2>
        </template>
            <div class="w-full">
                <div class="sm:w-full md:w-3/4 lg:w-1/2 mx-auto sm:px-6 lg:px-8 mt-4">
                    <div class="text-sm text-gray-600 p-4">
                        <p>
                            <b>Predictions</b> - how many games (matches) the user has put predictions for <br><br>
                            <b>Outcomes</b> - how many games has the user predicted the outcome for - i.e. not the exact result, but only predicted Win, Loose or Draw <br>
                            <b>Results</b> - how many games has the user predicted the exact result (score) for <br><br>
                            <b>Points</b> - calculation of total points, where an Outcome is 1 point, and a Result is 3 points
                        </p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 m-4 relative mt-4">
                        <table class="table-auto w-full divide-y divide-gray-300" cellpadding="10">
                            <thead class="bg-gray-100">
                                <tr class="h-8">
                                    <th class="text-left text-sm font-bold text-gray-900">User</th>
                                    <th class="text-right text-sm font-bold text-gray-900">Predictions</th>
                                    <th class="text-right text-sm font-bold text-gray-900">Outcomes</th>
                                    <th class="text-right text-sm font-bold text-gray-900">Results</th>
                                    <th class="text-right text-sm font-bold text-gray-900">Points</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr v-for="(user, index) in data" :key="index" class="h-8" :class="index % 2 === 0 ? undefined : 'bg-gray-100'">
                                    <td class="text-left text-sm font-medium text-gray-900">
                                        {{user.user.name}}
                                    </td>
                                    <td class="text-right text-sm text-gray-500">{{user.predictions}}</td>
                                    <td class="text-right text-sm text-gray-500">{{user.outcomes}}</td>
                                    <td class="text-right text-sm text-gray-500">{{user.results}}</td>
                                    <td class="text-right text-sm text-gray-500">{{user.points}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';

export default {
    components: {
        AuthenticatedLayout,
        Head,
    },

    props: {
        data: Object,
    },

    setup(props) {

        return {
        }

    }
    
}

</script>