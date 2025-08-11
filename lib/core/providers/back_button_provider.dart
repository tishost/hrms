import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

class BackButtonState {
  final DateTime? lastBackPressTime;
  
  BackButtonState({this.lastBackPressTime});
}

class BackButtonNotifier extends StateNotifier<BackButtonState> {
  BackButtonNotifier() : super(BackButtonState());

  Future<bool> handleBackPress(BuildContext context, String currentPath) async {
    // List of all dashboard routes
    const dashboardRoutes = [
      '/dashboard', 
      '/tenant-dashboard',
      '/admin-dashboard'
    ];

    // First check if we can pop any nested navigator
    if (context.canPop()) {
      context.pop();
      return false;
    }

    // Only show exit confirmation on dashboard screens
    if (dashboardRoutes.contains(currentPath)) {
      final now = DateTime.now();
      if (state.lastBackPressTime == null || 
          now.difference(state.lastBackPressTime!) > const Duration(seconds: 2)) {
        state = BackButtonState(lastBackPressTime: now);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Press back again to exit'),
            duration: const Duration(seconds: 2),
          ),
        );
        return false;
      }
      return true; // Allow exit
    }
    return true; // For non-dashboard screens
  }
}

final backButtonProvider = StateNotifierProvider<BackButtonNotifier, BackButtonState>(
  (ref) => BackButtonNotifier(),
);
